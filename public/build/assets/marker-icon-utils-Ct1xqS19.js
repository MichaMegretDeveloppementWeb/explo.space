import{L as t}from"./map-responsive-config-DdMpAmdA.js";function n(){if(document.getElementById("explo-map-svg-defs"))return;const e=document.createElementNS("http://www.w3.org/2000/svg","svg");e.setAttribute("id","explo-map-svg-defs"),e.style.position="absolute",e.style.width="0",e.style.height="0",e.style.visibility="hidden",e.innerHTML=`
        <defs>
            <!-- Filtre d'ombre portée pour marqueurs normaux -->
            <filter id="marker-shadow-normal" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur in="SourceAlpha" stdDeviation="1"/>
                <feOffset dx="0" dy="1" result="offsetblur"/>
                <feComponentTransfer>
                    <feFuncA type="linear" slope="0.3"/>
                </feComponentTransfer>
                <feMerge>
                    <feMergeNode/>
                    <feMergeNode in="SourceGraphic"/>
                </feMerge>
            </filter>

            <!-- Filtre d'ombre portée pour marqueurs featured -->
            <filter id="marker-shadow-featured" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur in="SourceAlpha" stdDeviation="1"/>
                <feOffset dx="0" dy="1" result="offsetblur"/>
                <feComponentTransfer>
                    <feFuncA type="linear" slope="0.3"/>
                </feComponentTransfer>
                <feMerge>
                    <feMergeNode/>
                    <feMergeNode in="SourceGraphic"/>
                </feMerge>
            </filter>
        </defs>
    `,document.body.appendChild(e)}function f(e=27,r=42){n();const o=`
        <svg width="${e}" height="${r}" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
            <!-- Pin bleu -->
            <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                  fill="#3b82f6"
                  stroke="#ffffff00"
                  stroke-width="1.5"
                  filter="url(#marker-shadow-normal)"/>

            <!-- Point blanc au centre -->
            <circle cx="12.5" cy="12.5" r="5" fill="white"/>
        </svg>
    `;return t.divIcon({html:o,className:"normal-marker-icon",iconSize:[e,r],iconAnchor:[e/2,r],popupAnchor:[0,-r+5]})}function s(e=27,r=42){n();const o=`
        <svg width="${e}" height="${r}" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
            <!-- Pin rouge -->
            <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                  fill="#ef4444"
                  stroke="#ffffff00"
                  stroke-width="1.5"
                  filter="url(#marker-shadow-normal)"/>

            <!-- Point blanc au centre -->
            <circle cx="12.5" cy="12.5" r="5" fill="white"/>
        </svg>
    `;return t.divIcon({html:o,className:"old-marker-icon",iconSize:[e,r],iconAnchor:[e/2,r],popupAnchor:[0,-r+5]})}export{s as a,f as c};
