#!/usr/bin/env python3
import sys
import json
import cv2
import numpy as np
import os
import glob

def load_face_models(student_ids):
    """Load face models for given student IDs"""
    models = {}
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    models_dir = os.path.join(base_dir, 'face_models')
    
    for student_id in student_ids:
        model_file = os.path.join(models_dir, f"{student_id}.json")
        if os.path.exists(model_file):
            with open(model_file, 'r') as f:
                models[student_id] = json.load(f)
    
    return models

def extract_face_encoding(image):
    """Extract face encoding from image"""
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    
    faces = face_cascade.detectMultiScale(gray, 1.3, 5)
    
    if len(faces) == 0:
        return []
    
    encodings = []
    for (x, y, w, h) in faces:
        face_roi = gray[y:y+h, x:x+w]
        face_roi = cv2.resize(face_roi, (100, 100))
        face_roi = cv2.equalizeHist(face_roi)
        face_encoding = face_roi.flatten().astype(np.float32)
        
        if np.linalg.norm(face_encoding) > 0:
            face_encoding = face_encoding / np.linalg.norm(face_encoding)
        
        encodings.append(face_encoding.tolist())
    
    return encodings

def compare_faces(known_encodings, face_encoding, tolerance=0.6):
    """Compare face encoding with known encodings"""
    face_encoding = np.array(face_encoding)
    
    for known_encoding in known_encodings:
        known_encoding = np.array(known_encoding)
        distance = np.linalg.norm(known_encoding - face_encoding)
        
        if distance < tolerance:
            return True
    
    return False

def main():
    if len(sys.argv) < 3:
        print(json.dumps({'success': False, 'message': 'Missing arguments'}))
        return
    
    image_path = sys.argv[1]
    student_ids = sys.argv[2].split(',')
    
    # Load image
    image = cv2.imread(image_path)
    if image is None:
        print(json.dumps({'success': False, 'message': 'Could not load image'}))
        return
    
    # Extract face encodings from image
    face_encodings = extract_face_encoding(image)
    
    if not face_encodings:
        print(json.dumps({'success': False, 'message': 'No faces detected'}))
        return
    
    # Load student face models
    models = load_face_models(student_ids)
    
    # Compare faces
    recognized_ids = []
    for student_id, model in models.items():
        if 'encodings' in model:
            for face_encoding in face_encodings:
                if compare_faces(model['encodings'], face_encoding):
                    if student_id not in recognized_ids:
                        recognized_ids.append(student_id)
                    break
    
    print(json.dumps({
        'success': True,
        'recognized_ids': recognized_ids,
        'faces_detected': len(face_encodings)
    }))

if __name__ == '__main__':
    main()