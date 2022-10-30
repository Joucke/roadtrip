import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import * as L from 'leaflet'
import "leaflet.markercluster";

import iconUrl from 'leaflet/dist/images/marker-icon.png'
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png'
import shadowUrl from 'leaflet/dist/images/marker-shadow.png'

// const markerIcon = L.icon({
//   ...L.Icon.Default,
//   iconUrl,
//   iconRetinaUrl,
// })
// console.log(L.Icon.Default.prototype.options)
L.Icon.Default.prototype.options.iconUrl = iconUrl;
L.Icon.Default.prototype.options.iconRetinaUrl = iconRetinaUrl;
L.Icon.Default.prototype.options.shadowUrl = shadowUrl;
L.Icon.Default.imagePath = '';

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
      m.addLayer(L.marker([r.lat, r.long]))//, {icon: markerIcon}))
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
      m.addLayer(L.marker([r.lat, r.long]))//, {icon: markerIcon}))
      this.map.addLayer(m)
    })
  }
})

Alpine.store('region', {
  create(url, csrf, region) {
    const data = {
      title: region.display_name.split(', ')[0],
      lat: region.lat,
      long: region.lon,
      box: JSON.stringify(region.boundingbox),
    }
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({data, _token: csrf})
    }).then(response => response.json())
  },
  update(url, csrf, data) {
    return fetch(url, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ ...data, _token: csrf }),
    }).then(response => response.json())
  }
})

Alpine.store('search', {
  timeout: null,
  results: {
    regions: [],
  },
  region(title) {
    clearTimeout(this.timeout)
    this.timeout = setTimeout(() => {
      if (title == '') {
        this.results.regions = []
        return
      }
      fetch(`/geocode-search?q=${title}`)
        .then(resp => resp.json())
        .then(data => {
          this.results.regions = data.length > 3 ? data.filter(r => ['natural', 'boundary'].includes(r.class) || ['region', 'mountain_range'].includes(r.type)) : data
        })
    }, 300);
  }

})
