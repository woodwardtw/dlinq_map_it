const map = L.map('map').setView([32.825512, -30.535592], 2.6);

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1Ijoid29vZHdhcmR0dyIsImEiOiJjanNhaTVheGgwYTB4NDRwb25qN3lrbjkzIn0.Vi6Vk1OENLLYV1lWVNYSTw'
}).addTo(map);

let catId = '';
const mapBox = document.querySelector('#map');
//console.log(mapBox.dataset.cat);
if(mapBox.dataset.cat)
  {
    catId = '&categories=' + mapBox.dataset.cat
  }
const wpJson = document.querySelector('link[rel="https://api.w.org/"]').href + 'wp/v2/posts?per_page=99' + catId;
// console.log(wpJson);

fetch(wpJson).then(response => response.json()).then(data => markerMaker(data));



function markerMaker(data){
    var markers = L.markerClusterGroup({
      spiderfyOnMaxZoom: true,
      showCoverageOnHover: false,
      zoomToBoundsOnClick: false,
      maxClusterRadius: 40,
    });
    data.forEach((item, index) => {
      const title = item.title.rendered;      
      const lat = item.lat;
      const long = item.lng;
      const marker = L.marker([lat, long],{autoPan: true, keepInView: true}).addTo(map);
      let imgHtml = '';
      let bioHtml = '';
      if(item.f_img){
        const imgUrl = item.f_img;
        imgHtml = `<img class="pic" src="${imgUrl}" alt="Picture of the home town for ${title}">`;
      }
      if(item.content.rendered){
        const content = item.content.rendered;
        bioHtml = `<div class="bio">${content}</div>`;
      }
      if (lat != '' && long != ''){
        marker.bindPopup(`<h2 class="popup-name">${title}</h2> ${imgHtml}${bioHtml}`);
      }
      markers.addLayer(marker);
  });
    //map.addLayer(markers);
}


// var markers = L.markerClusterGroup();
// markers.addLayer(L.marker(getRandomLatLng(map)));
// ... Add more layers ...
// map.addLayer(markers);