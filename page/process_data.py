# process_data.py

import os
from PIL import Image, ImageDraw, ImageFont
import sys

def text_to_image(text, output_image_filename, font_path=None, font_size=24, image_size=(800, 600), text_color=(0, 0, 0), bg_color=(255, 255, 255)):
    # Tạo một hình ảnh mới
    image = Image.new('RGB', image_size, bg_color)
    draw = ImageDraw.Draw(image)

    # Sử dụng một font
    if font_path:
        font = ImageFont.truetype(font_path, font_size)
    else:
        font = ImageFont.load_default()

    # Vẽ văn bản lên hình ảnh
    draw.text((10, 10), text, fill=text_color, font=font)

    # Lưu hình ảnh
    current_directory = os.path.dirname(__file__)
    output_image_path = os.path.join(current_directory, output_image_filename)
    image.save(output_image_path)

    # Trả về đường dẫn của hình ảnh được tạo
    return output_image_path

if __name__ == "__main__":
    # Lấy đối số đầu vào từ dòng lệnh
    search_query = sys.argv[1]
    id_search=sys.argv[2]
    # Tên của hình ảnh đầu ra
    output_image_filename = f"text_image{id_search}.png"

    # Đường dẫn đến font, bạn có thể thay đổi
    font_path = "arial.ttf"

    # Gọi hàm để chuyển đổi văn bản thành hình ảnh và xuất ảnh ra cùng thư mục
    text_to_image(search_query, output_image_filename, font_path)