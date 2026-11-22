import {CanvasImages} from "@/model/common/functions/canvas-image-utils.js";

export const doorsX = 342;
export const doorsY = 200;
export const doorsWidth = 295;
export const doorsHeight = 633;

/** Draws the configurator canvas using preloaded images. */
export function generateCanvas(
    canvas: HTMLCanvasElement,
    images: CanvasImages,
    isMobile: boolean = false
): void {
    const {room, material, door, frame, frameColor, handle} = images;
    const originalCanvas = document.createElement("canvas");
    originalCanvas.width = room.width;
    originalCanvas.height = room.height;
    const ctx = originalCanvas.getContext("2d")!;

    ctx.drawImage(room, 0, 0);
    ctx.drawImage(material, doorsX, doorsY, doorsWidth, doorsHeight);
    ctx.drawImage(door, doorsX, doorsY, doorsWidth, doorsHeight);
    if (frameColor) ctx.drawImage(frameColor, doorsX, doorsY, doorsWidth, doorsHeight);
    if (frame) ctx.drawImage(frame, doorsX, doorsY, doorsWidth, doorsHeight);
    if (handle) ctx.drawImage(handle, doorsX, doorsY, doorsWidth, doorsHeight);

    const vCtx = canvas.getContext("2d")!;

    if (isMobile) {
        const targetAspect = 9 / 16;
        const paddingFactor = 1.7;

        const cropHeight = doorsHeight * paddingFactor;
        const cropWidth = cropHeight * targetAspect;
        const doorCenterX = doorsX + doorsWidth / 2;
        const doorCenterY = doorsY + doorsHeight / 2;

        let cropX = doorCenterX - cropWidth / 2;
        let cropY = doorCenterY - cropHeight / 2;

        cropX = Math.max(0, Math.min(cropX, originalCanvas.width - cropWidth));
        cropY = Math.max(0, Math.min(cropY, originalCanvas.height - cropHeight));

        canvas.width = 1080;
        canvas.height = 1920;

        vCtx.drawImage(
            originalCanvas,
            cropX, cropY, cropWidth, cropHeight,
            0, 0, canvas.width, canvas.height
        );
    } else {
        canvas.width = room.width;
        canvas.height = room.height;

        vCtx.drawImage(originalCanvas, 0, 0, canvas.width, canvas.height);
    }
}

export function getButtonPosition(
    dx: number,
    dy: number,
    dw: number,
    dh: number,
    canvas: HTMLCanvasElement,
    buttonWidth: number, // 👈 NEW: pass actual button width
    backgroundImgWidth: number = 1982,
    backgroundImgHeight: number = 1031
): { x: number; y: number } {
    // scale like object-fit: cover
    const scale = Math.max(
        canvas.clientWidth / backgroundImgWidth,
        canvas.clientHeight / backgroundImgHeight
    );

    const scaledHeight = backgroundImgHeight * scale;
    const offsetY = (canvas.clientHeight - scaledHeight) / 2;

    // ✅ CENTER of door instead of left edge
    const doorCenterX = dx * scale + (dw * scale) / 2;

    // ✅ center button horizontally
    const x = doorCenterX - buttonWidth / 2;

    const spaceBelowDoorsOffset = 20;
    const y =
        dy * scale +
        dh * scale +
        spaceBelowDoorsOffset +
        offsetY;

    return {x, y};
}
