import cv2
import torch
import torch.backends.cudnn as cudnn
import numpy as np
import os
import sys

from utils.datasets import letterbox
from utils.general import check_img_size, non_max_suppression, scale_coords
from utils.plots import plot_one_box
from utils.torch_utils import select_device

def draw_point(coords, img, label=None, color=None, line_thickness=None):
    tl = line_thickness or round(0.002 * (img.shape[0] + img.shape[1]) / 2) + 1  # line/font thickness
    cv2.circle(img, coords, 15, color, -1)
    if label:
        tf = max(tl - 1, 1)  # font thickness
        t_size = cv2.getTextSize(label, 0, fontScale=tl / 3, thickness=tf)[0]
        c2 = coords[0] + t_size[0], coords[1] - t_size[1] - 3
        cv2.rectangle(img, coords, c2, color, -1, cv2.LINE_AA)  # filled
        cv2.putText(img, label, (coords[0], coords[1] - 2), 0, tl / 3, (0, 0, 0), thickness=tf, lineType=cv2.LINE_AA)

weight = 'model.pt'
img_path = 'rgb_img.png'
depth_img_path = 'depth_img.png'
imgsz = 640
augment = False
agnostic_nms = False
deviceToUse = '0' #'cpu'
conf_thres = 0.25
iou_thres = 0.45
classes = None

if (deviceToUse == 'cpu'):
    device = torch.device("cpu")
else:
    if torch.cuda.is_available():
        os.environ['CUDA_VISIBLE_DEVICES'] = deviceToUse
        device = torch.device("cuda")
    else:
        raise Exception('Invalid device selected.')
half = device.type != 'cpu'  # half precision only supported on CUDA

model = torch.load(weight, map_location=device)  # load
model = model['ema' if model.get('ema') else 'model'].float().fuse().eval()  # FP32 model
stride = int(model.stride.max())  # model stride
imgsz = check_img_size(imgsz, s=stride)  # check img_size
if half: model.half()  # to FP16

names = model.module.names if hasattr(model, 'module') else model.names
colors = [[np.random.randint(0, 255) for _ in range(3)] for _ in names]

img_base = cv2.imread(img_path)  # BGR
assert img_base is not None, 'Image Not Found ' + img_path
img_np = letterbox(img_base, imgsz, stride=stride)[0] # Padded resize
# Convert
img_np = img_np[:, :, ::-1].transpose(2, 0, 1)  # BGR to RGB, to 3x416x416
img_np = np.ascontiguousarray(img_np)

model(torch.zeros(1, 3, imgsz, imgsz).to(device).type_as(next(model.parameters())))  # run once

### No warmup here
###

img = torch.from_numpy(img_np).to(device)
img = img.half() if half else img.float()  # uint8 to fp16/32
img /= 255.0  # 0 - 255 to 0.0 - 1.0
if img.ndimension() == 3:
    img = img.unsqueeze(0)

with torch.no_grad():   # Calculating gradients would cause a GPU memory leak
    pred = model(img, augment=augment)[0]
# Apply NMS
pred = non_max_suppression(pred, conf_thres, iou_thres, classes=classes, agnostic=agnostic_nms)

out_list = []

for i, det in enumerate(pred):  # detections per image
    gn = torch.tensor(img_base.shape)[[1, 0, 1, 0]]  # normalization gain whwh
    if len(det):
        # Rescale boxes from img_size to img_base size
        det[:, :4] = scale_coords(img.shape[2:], det[:, :4], img_base.shape).round()

        depth_img = cv2.imread(depth_img_path, -1) # -1 loads 'as is'

        for *xyxy, conf, cls in reversed(det):
            # draws bounding boxes
            # label = f'{names[int(cls)]} {conf:.2f}'
            # plot_one_box(xyxy, img_base, label=label, color=colors[int(cls)], line_thickness=3)

            # gets the middle pixel at the height of the camera
            if (xyxy[1] < img_base.shape[0] // 2 and xyxy[3] > img_base.shape[0] // 2):
                point = ( int((xyxy[0] + xyxy[2]) / 2), img_base.shape[0] // 2 ) # camera eye level
            else:
                point = ( int((xyxy[0] + xyxy[2]) / 2), int((xyxy[1] + xyxy[3]) / 2) ) # center of object bbox
            dist = depth_img[point[1], point[0]] # distance in mm

            if (dist == 0):
                # searches for actual values around the point
                min_val = None
                for y in range(point[1] - 20, point[1] + 20):
                    for x in range(int(xyxy[0]), int(xyxy[2])):
                        px = depth_img[y, x]
                        if px != 0 and (min_val is None or px < min_val):
                            min_val = px
                            point = (x, y)
                dist = min_val

            draw_point(point, img_base, f'{dist}mm', (255, 255, 255))
        
        # cv2.imwrite('dimg.png', img_base)
        cv2.imshow('Image', img_base)
        cv2.waitKey(0)