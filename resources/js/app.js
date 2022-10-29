import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import * as L from 'leaflet'
import "leaflet.markercluster";

Alpine.store('map', {
  map: null,
  show(trip) {
    if (trip.regions.length) {
      const mapBounds = [
          [
            Math.min(...trip.regions.map(r => r.lat)),
            Math.min(...trip.regions.map(r => r.long)),
          ],
          [
            Math.max(...trip.regions.map(r => r.lat)),
            Math.max(...trip.regions.map(r => r.long)),
          ]
      ];

      this.map = L.map('map').fitBounds(mapBounds)
    } else {
      this.map = L.map('map').fitWorld()
    }
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(this.map);

    trip.regions.forEach(r => {
      const m = L.markerClusterGroup()
      m.addLayer(L.marker([r.lat, r.long]))
      this.map.addLayer(m)
    })
  },
  update(trip) {
    const mapBounds = [
      [
        Math.min(...trip.regions.map(r => r.lat)),
        Math.min(...trip.regions.map(r => r.long)),
      ],
      [
        Math.max(...trip.regions.map(r => r.lat)),
        Math.max(...trip.regions.map(r => r.long)),
      ]
    ];

    this.map.fitBounds(mapBounds);

    trip.regions.forEach(r => {
      const m = L.markerClusterGroup()
      m.addLayer(L.marker([r.lat, r.long]))
      this.map.addLayer(m)
    })
  }
})

Alpine.store('region', {
  patch(url, csrf, title) {
    return fetch(url, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ title, _token: csrf }),
    }).then(response => response.json())
  }
})
