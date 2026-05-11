import {updateForm} from "@/model/api/rest.js";

export async function devFunction() {
    const test = '{\n' +
        '  "priceOffer": {\n' +
        '    "address": {\n' +
        '      "city": "City",\n' +
        '      "district": "banska-bystrica",\n' +
        '      "street": "Street",\n' +
        '      "streetNumber": "1",\n' +
        '      "zipCode": "97901"\n' +
        '    },\n' +
        '    "assemblyDoorsCount": 3,\n' +
        '    "assemblyHandlesRosettesCount": 3,\n' +
        '    "contact": {\n' +
        '      "email": "aa@aa.sk",\n' +
        '      "fullName": "Meno",\n' +
        '      "phoneNumber": "+42111111111"\n' +
        '    },\n' +
        '    "currency": "EUR",\n' +
        '    "doors": [\n' +
        '      {\n' +
        '        "category": "best-selling",\n' +
        '        "material": "lamino-jelsa",\n' +
        '        "type": "v1",\n' +
        '        "width": "",\n' +
        '        "isDoorFrameEnabled": true,\n' +
        '        "isDtdSelected": null\n' +
        '      },\n' +
        '      {\n' +
        '        "category": "best-selling",\n' +
        '        "material": "lamino-jelsa",\n' +
        '        "type": "v1",\n' +
        '        "width": "",\n' +
        '        "isDoorFrameEnabled": true,\n' +
        '        "isDtdSelected": null\n' +
        '      },\n' +
        '      {\n' +
        '        "category": "wien_1",\n' +
        '        "material": "wien-biela",\n' +
        '        "type": "W1_L2_K2_O2",\n' +
        '        "width": "W90",\n' +
        '        "isDoorFrameEnabled": false,\n' +
        '        "isDtdSelected": null\n' +
        '      }\n' +
        '    ],\n' +
        '    "customHandle": {\n' +
        '      "name": "Custom handle",\n' +
        '      "price": 1,\n' +
        '      "count": 2\n' +
        '    },\n' +
        '    "handle": {\n' +
        '      "count": 3,\n' +
        '      "id": "Alt_wien",\n' +
        '      "isCountDirty": false\n' +
        '    },\n' +
        '    "isAssemblyDoorsCountDirty": false,\n' +
        '    "isAssemblyHandlesRosettesCountDirty": false,\n' +
        '    "note": "Note",\n' +
        '    "technicalSurcharges": [\n' +
        '      {\n' +
        '        "id": "technical-surcharges-hollow-particle-board-filling",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-silicone-frame",\n' +
        '        "count": 3,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-silicone-wall-frame",\n' +
        '        "count": 3,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-carrying",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-thicker-walls",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-higher-doors",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-original-steel-door-frames-cladding",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "technical-surcharges-tiles-cutting",\n' +
        '        "count": 0,\n' +
        '        "isCountDirty": ""\n' +
        '      }\n' +
        '    ],\n' +
        '    "technicalSurchargesLineItems": [\n' +
        '      {\n' +
        '        "name": "Technical surcharge 1",\n' +
        '        "price": 1,\n' +
        '        "count": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Technical surcharge 2",\n' +
        '        "price": 2,\n' +
        '        "count": 2\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Technical surcharge 2",\n' +
        '        "price": 3,\n' +
        '        "count": 3\n' +
        '      }\n' +
        '    ],\n' +
        '    "rosettes": [\n' +
        '      {\n' +
        '        "id": "bb",\n' +
        '        "count": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "wc",\n' +
        '        "count": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "fab",\n' +
        '        "count": 0\n' +
        '      }\n' +
        '    ],\n' +
        '    "rosettesLineItems": [\n' +
        '      {\n' +
        '        "count": 1,\n' +
        '        "name": "Custom rosette",\n' +
        '        "price": 10\n' +
        '      },\n' +
        '      {\n' +
        '        "count": 2,\n' +
        '        "name": "Custom rosette 2",\n' +
        '        "price": 10\n' +
        '      }\n' +
        '    ],\n' +
        '    "selectedDoorsLineItems": [\n' +
        '      {\n' +
        '        "isDoorFrameEnabled": true,\n' +
        '        "name": "Custom door 1",\n' +
        '        "price": 100,\n' +
        '        "width": "W70"\n' +
        '      },\n' +
        '      {\n' +
        '        "isDoorFrameEnabled": false,\n' +
        '        "name": "Custom door 2",\n' +
        '        "price": 100,\n' +
        '        "width": "W80"\n' +
        '      }\n' +
        '    ],\n' +
        '    "aestheticAccessories": [\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-colored-hinge-caps",\n' +
        '        "count": 1,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-colorful-magnetic-locks",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-sliding-door-shells",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-colorful-hidden-hardware",\n' +
        '        "count": 5,\n' +
        '        "selectedPrice": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-thimble",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-spraying-surcharge",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-veneer-surcharge",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-glass-surcharge",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "aesthetic-accessories-door-ventilation-grilles",\n' +
        '        "count": 0,\n' +
        '        "selectedPrice": 0\n' +
        '      }\n' +
        '    ],\n' +
        '    "aestheticAccessoriesLineItems": [\n' +
        '      {\n' +
        '        "name": "Aesthetic accessory 1",\n' +
        '        "price": 1,\n' +
        '        "count": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Aesthetic accessory 2",\n' +
        '        "price": 2,\n' +
        '        "count": 2\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Aesthetic accessory 3",\n' +
        '        "price": 3,\n' +
        '        "count": 3\n' +
        '      }\n' +
        '    ],\n' +
        '    "specialSurcharges": [\n' +
        '      {\n' +
        '        "id": "special-surcharges-double-door-frame",\n' +
        '        "count": 1,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-sliding-doors-fittings-curtain-rod",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-minima",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-sliding-doors-hardware-built-in-housing",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-hidden-hinges",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-reversible-doors",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-double-leaf-non-rebated-door-with-frame",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-double-leaf-sliding-door-curtain",\n' +
        '        "count": 1,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-double-leaf-sliding-door-minima",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-frame-for-double-leaf-doors-in-case",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-barn-1",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-minima-black",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-door-to-case-with-frame",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-door-to-case-without-frame",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-double-leaf-door-to-case",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-folding-interior-doors",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-sliding-hardware-invisible",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-barn-4",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-barn-3",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      },\n' +
        '      {\n' +
        '        "id": "special-surcharges-barn-2",\n' +
        '        "count": 0,\n' +
        '        "isAssemblySelected": false,\n' +
        '        "isAssemblySelectedDirty": false\n' +
        '      }\n' +
        '    ],\n' +
        '    "specialSurchargesLineItems": [\n' +
        '      {\n' +
        '        "name": "Special surcharges 1",\n' +
        '        "price": 1,\n' +
        '        "count": 1\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Special surcharges 2",\n' +
        '        "price": 2,\n' +
        '        "count": 2\n' +
        '      },\n' +
        '      {\n' +
        '        "name": "Special surcharges 3",\n' +
        '        "price": 3,\n' +
        '        "count": 3\n' +
        '      }\n' +
        '    ]\n' +
        '  }\n' +
        '}'

    await updateForm(JSON.parse(test));
}