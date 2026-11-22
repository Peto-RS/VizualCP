import {Room} from "@/model/interior-doors/configurator/Room.js";
import {SelectedDoor} from "@/model/interior-doors/configurator/interface/SelectedDoor.js";
import {SelectedSecurityDoor} from "@/model/security-doors/interfaces/SelectedSecurityDoor.js";

export interface CanvasImageUrls {
    room: string
    material: string
    door: string
    frame?: string
    frameColor?: string
    handle?: string
}

export interface CanvasImages {
    room: HTMLImageElement
    material: HTMLImageElement
    door: HTMLImageElement
    frame: HTMLImageElement | null
    frameColor: HTMLImageElement | null
    handle: HTMLImageElement | null
}

export function composeCanvasImageUrls(
    baseUrl: string,
    selectedRoom: Room,
    selectedDoor: SelectedDoor | SelectedSecurityDoor
): CanvasImageUrls {
    const urls: CanvasImageUrls = {
        room: baseUrl + selectedRoom.imgSrc,
        door: `${baseUrl}/images/doors/${selectedDoor.categoryId}/${selectedDoor.type}.png`,
        material: "frameColor" in selectedDoor
            ? `${baseUrl}/images/security-doors/materials/front/${selectedDoor.material}.jpg`
            : `${baseUrl}/images/materials/${selectedDoor.material}.png`
    };

    if ("frameColor" in selectedDoor) {
        if (selectedDoor.frameColor) {
            urls.frameColor = `${baseUrl}/images/security-doors/frame-colors/${selectedDoor.frameColor}.png`;
        }
        urls.frame = `${baseUrl}/images/security-doors/frames/front.png`;
    } else {
        if (!selectedDoor.doorCategory.excludedDoorPartsFromCanvas?.includes("frame")) {
            urls.frame = `${baseUrl}/images/zarubna.png`;
        }
        if (!selectedDoor.doorCategory.excludedDoorPartsFromCanvas?.includes("handle") && selectedDoor.handleId) {
            urls.handle = `${baseUrl}/images/handles/${selectedDoor.handleId}.png`;
        }
    }

    return urls;
}

export async function downloadImages(urls: CanvasImageUrls): Promise<CanvasImages> {
    const [room, material, door, frame, frameColor, handle] = await Promise.all([
        loadImage(urls.room),
        loadImage(urls.material),
        loadImage(urls.door),
        loadOptionalImage(urls.frame),
        loadOptionalImage(urls.frameColor),
        loadOptionalImage(urls.handle)
    ]);

    return {room, material, door, frame, frameColor, handle};
}

function loadOptionalImage(src?: string): Promise<HTMLImageElement | null> {
    return src ? loadImage(src) : Promise.resolve(null);
}

function loadImage(src: string): Promise<HTMLImageElement> {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = () => reject(new Error(`Failed to load image: ${src}`));
        img.src = src;
    });
}
