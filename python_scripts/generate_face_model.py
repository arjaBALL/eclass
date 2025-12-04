#!/usr/bin/env python3
import sys
import json
import cv2
import numpy as np
import os
import glob

def generate_face_model(student_id, student_name):
    """
    Generate face recognition model from captured images
    
    Parameters:
    student_id (str): Student ID
    student_name (str): Student full name
    
    Returns:
    dict: Success status and model information
    """
    # Get base directory and paths
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    face_data_dir = os.path.join(base_dir, 'face_data', student_id)
    models_dir = os.path.join(base_dir, 'face_models')
    
    # Create models directory if it doesn't exist
    if not os.path.exists(models_dir):
        os.makedirs(models_dir, mode=0o755)
    
    # Check if face data directory exists
    if not os.path.exists(face_data_dir):
        return {
            'success': False,
            'message': f'Face data directory not found for student {student_id}'
        }
    
    # Get all images
    image_files = glob.glob(os.path.join(face_data_dir, '*.jpg'))
    
    if len(image_files) == 0:
        return {
            'success': False,
            'message': 'No images found in face data directory'
        }
    
    # Initialize face detector
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    
    # Extract face encodings from all images
    encodings = []
    valid_samples = 0
    
    for image_file in image_files:
        image = cv2.imread(image_file)
        
        if image is None:
            continue
        
        # Convert to grayscale
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        
        # Detect faces
        faces = face_cascade.detectMultiScale(gray, 1.3, 5)
        
        if len(faces) == 0:
            continue
        
        # Use the first face detected
        x, y, w, h = faces[0]
        face_roi = gray[y:y+h, x:x+w]
        
        # Resize to standard size
        face_roi = cv2.resize(face_roi, (100, 100))
        
        # Histogram equalization for better recognition
        face_roi = cv2.equalizeHist(face_roi)
        
        # Convert to 1D array (encoding)
        face_encoding = face_roi.flatten().astype(np.float32)
        
        # Normalize the encoding
        if np.linalg.norm(face_encoding) > 0:
            face_encoding = face_encoding / np.linalg.norm(face_encoding)
        
        encodings.append(face_encoding.tolist())
        valid_samples += 1
    
    if valid_samples == 0:
        return {
            'success': False,
            'message': 'No valid face samples found in images'
        }
    
    # Create model data
    from datetime import datetime
    
    model_data = {
        'student_id': student_id,
        'student_name': student_name,
        'encodings': encodings,
        'samples_collected': valid_samples,
        'model_version': '2.0',
        'created_date': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    }
    
    # Save model to JSON file
    model_file = os.path.join(models_dir, f'{student_id}.json')
    
    try:
        with open(model_file, 'w') as f:
            json.dump(model_data, f, indent=2)
    except Exception as e:
        return {
            'success': False,   
            'message': f'Failed to save model file: {str(e)}'
        }
    
    return {
        'success': True,
        'message': 'Face model generated successfully',
        'samples_collected': valid_samples,
        'model_version': '2.0',
        'model_file': model_file
    }

if __name__ == '__main__':
    if len(sys.argv) != 3:
        print(json.dumps({
            'success': False,
            'message': 'Usage: generate_face_model.py <student_id> <student_name>'
        }))
        sys.exit(1)
    
    student_id = sys.argv[1]
    student_name = sys.argv[2]
    
    result = generate_face_model(student_id, student_name)
    print(json.dumps(result))