import {Room} from "@/model/api/res/configurator/Room.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";

export const doorsX = 342;
export const doorsY = 200;
export const doorsWidth = 295;
export const doorsHeight = 633;

/**
 * Draws the configurator canvas for desktop or mobile directly into the given canvas.
 * Returns the screen coordinates for the button (centered below the door).
 */
export async function generateCanvas(
    canvas: HTMLCanvasElement,
    baseUrl: string,
    selectedRoom: Room | null,
    selectedDoor: SelectedDoor | null,
    isMobile: boolean = false
): Promise<void> {
    if (!selectedRoom || !selectedDoor?.categoryId || !selectedDoor?.material) return;

    try {
        // --- Load all images ---
        const [
            room,
            material,
            door,
            frame,
            handle
        ] = await Promise.all([
            loadImage(baseUrl + selectedRoom.imgSrc),
            loadImage(`${baseUrl}/images/materials/${selectedDoor.material}.png`),
            loadImage(`${baseUrl}/images/doors/${selectedDoor.categoryId}/${selectedDoor.type}.png`),
            !selectedDoor.doorCategory.excludedDoorPartsFromCanvas.includes("frame")
                ? loadImage(`${baseUrl}/images/zarubna.png`)
                : Promise.resolve(null),
            (!selectedDoor.doorCategory.excludedDoorPartsFromCanvas.includes("handle") && selectedDoor.handleId)
                ? loadImage(`${baseUrl}/images/handles/${selectedDoor.handleId}.png`)
                : Promise.resolve(null)
        ]);

        // --- Draw full-size original canvas offscreen ---
        const originalCanvas = document.createElement("canvas");
        originalCanvas.width = room.width;
        originalCanvas.height = room.height;
        const ctx = originalCanvas.getContext("2d")!;

        ctx.drawImage(room, 0, 0);
        ctx.drawImage(material, doorsX, doorsY, doorsWidth, doorsHeight);
        ctx.drawImage(door, doorsX, doorsY, doorsWidth, doorsHeight);
        if (frame) ctx.drawImage(frame, doorsX, doorsY, doorsWidth, doorsHeight);
        if (handle) ctx.drawImage(handle, doorsX, doorsY, doorsWidth, doorsHeight);

        // --- Draw onto visible canvas ---
        const vCtx = canvas.getContext("2d")!;

        if (isMobile) {
            // Mobile 9:16 crop
            const targetAspect = 9 / 16;
            const paddingFactor = 1.7; // optional padding around door

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
            // Desktop: full canvas
            canvas.width = room.width;
            canvas.height = room.height;

            vCtx.drawImage(originalCanvas, 0, 0, canvas.width, canvas.height);
        }

    } catch (error: unknown) {
        console.error("generateCanvas failed:", error);
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

function loadImage(src: string): Promise<HTMLImageElement> {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}