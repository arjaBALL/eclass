#!/usr/bin/env python3
import sys
import cv2
import numpy as np
import json

def validate_face(image_path):
    """
    Validates if an image contains a suitable face for recognition.
    
    Parameters:
    image_path (str): Path to the image file
    
    Returns:
    dict: Results of validation with success status and message
    """
    # Load image
    image = cv2.imread(image_path)
    if image is None:
        return {'success': False, 'message': 'Could not load image'}
    
    # Convert to grayscale
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    
    # Check image brightness
    avg_brightness = np.mean(gray)
    if avg_brightness < 50:
        return {'success': False, 'message': 'Image too dark'}
    
    # Check image contrast
    std_dev = np.std(gray)
    if std_dev < 30:
        return {'success': False, 'message': 'Image has low contrast'}
    
    # Check image blur
    laplacian_var = cv2.Laplacian(gray, cv2.CV_64F).var()
    if laplacian_var < 100:
        return {'success': False, 'message': 'Image too blurry'}
    
    # Load face detector
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    
    # Detect faces
    faces = face_cascade.detectMultiScale(gray, 1.3, 5)
    
    if len(faces) == 0:
        return {'success': False, 'message': 'No face detected'}
    
    if len(faces) > 1:
        return {'success': False, 'message': 'Multiple faces detected'}
    
    # Check face size and position
    x, y, w, h = faces[0]
    face_size = w * h
    image_size = image.shape[0] * image.shape[1]
    face_ratio = face_size / image_size
    
    # Face should be at least 10% of the image
    if face_ratio < 0.1:
        return {'success': False, 'message': 'Face too small'}
    
    # Check if face is centered
    center_x = x + w/2
    center_y = y + h/2
    image_center_x = image.shape[1]/2
    image_center_y = image.shape[0]/2
    
    # Distance from center
    distance_from_center = np.sqrt((center_x - image_center_x)**2 + (center_y - image_center_y)**2)
    max_distance = np.sqrt((image_center_x)**2 + (image_center_y)**2) * 0.3  # 30% of max possible distance
    
    if distance_from_center > max_distance:
        return {'success': False, 'message': 'Face not centered'}
    
    # All checks passed
    return {'success': True, 'message': 'Face validation successful'}

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print(json.dumps({'success': False, 'message': 'Invalid arguments'}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    result = validate_face(image_path)
    print(json.dumps(result))