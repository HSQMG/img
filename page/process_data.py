# process_data.py

import os
from PIL import Image, ImageDraw, ImageFont
import sys
import requests
import io
from PIL import Image

API_URL = "https://api-inference.huggingface.co/models/ehristoforu/dalle-3-xl-v2"
headers = {"Authorization": "Bearer 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"}

def query(payload):
	response = requests.post(API_URL, headers=headers, json=payload)
	return response.content

if __name__ == "__main__":
    # Lấy đối số đầu vào từ dòng lệnh
    search_query = sys.argv[1]
    id_search=sys.argv[2]
    # Tên của hình ảnh đầu ra
    output_image_filename = f"text_image{id_search}.png"
    image_bytes = query({
        "inputs": search_query,
    })
    image = Image.open(io.BytesIO(image_bytes))
    image = image.resize((800, 800))
    image.save(output_image_filename)