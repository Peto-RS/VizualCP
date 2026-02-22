import {Room} from "@/model/api/res/configurator/Room.js";
import {SelectedDoor} from "@/model/interface/configurator/SelectedDoor.js";

export async function generateCanvas(
    baseUrl: string,
    selectedRoom: Room | null,
    selectedDoor: SelectedDoor | null,
    isMobile: boolean = false
): Promise<string | null> {

    if (!selectedRoom || !selectedDoor?.category || !selectedDoor?.material) {
        return Promise.resolve(null);
    }

    try {
        const [
            room,
            material,
            door,
            frame,
            handle
        ] = await Promise.all([
            loadImage(baseUrl + selectedRoom.imgSrc),
            loadImage(`${baseUrl}/images/materials/${selectedDoor.material}.png`),
            loadImage(`${baseUrl}/images/doors/${selectedDoor.category}/${selectedDoor.type}.png`),
            loadImage(`${baseUrl}/images/zarubna.png`),
            selectedDoor.handle
                ? loadImage(`${baseUrl}/images/handles/${selectedDoor.handle}.png`)
                : Promise.resolve(null)
        ]);

        // --- FULL CANVAS ---
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d")!;
        canvas.width = room.width;
        canvas.height = room.height;

        const dx = 342;
        const dy = 200;
        const dw = 295;
        const dh = 633;

        ctx.drawImage(room, 0, 0);
        ctx.drawImage(material, dx, dy, dw, dh);
        ctx.drawImage(door, dx, dy, dw, dh);
        ctx.drawImage(frame, dx, dy, dw, dh);

        if (handle) {
            ctx.drawImage(handle, dx, dy, dw, dh);
        }

        if (!isMobile) {
            return canvas.toDataURL("image/png");
        }

        // --- MOBILE 9:16 CROP ---
        const targetAspect = 9 / 16;

        // Add padding around door (adjust this!)
        const paddingFactor = 1.7; // 1.2–1.6 works well

        let cropHeight = dh * paddingFactor;
        let cropWidth = cropHeight * targetAspect;

        const doorCenterX = dx + dw / 2;
        const doorCenterY = dy + dh / 2;

        let cropX = doorCenterX - cropWidth / 2;
        let cropY = doorCenterY - cropHeight / 2;

// Clamp to canvas bounds
        cropX = Math.max(0, Math.min(cropX, canvas.width - cropWidth));
        cropY = Math.max(0, Math.min(cropY, canvas.height - cropHeight));

        const mobileCanvas = document.createElement("canvas");
        const mobileCtx = mobileCanvas.getContext("2d")!;

        mobileCanvas.width = 1080;
        mobileCanvas.height = 1920;

        mobileCtx.drawImage(
            canvas,
            cropX, cropY, cropWidth, cropHeight,
            0, 0, 1080, 1920
        );

        return mobileCanvas.toDataURL("image/png");

    } catch (error: unknown) {
        throw new Error("Image loading failed: " + JSON.stringify(error));
    }
}

function loadImage(src: string): Promise<HTMLImageElement> {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}