var map = L.map('map').setView([32.825512, -30.535592], 2.6);

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1Ijoid29vZHdhcmR0dyIsImEiOiJjanNhaTVheGgwYTB4NDRwb25qN3lrbjkzIn0.Vi6Vk1OENLLYV1lWVNYSTw'
}).addTo(map);

//var WpJsonUrl = document.querySelector('link[rel="https://api.w.org/"]').href

const wpJson = document.querySelector('link[rel="https://api.w.org/"]').href + 'wp/v2/posts';
console.log(wpJson);

fetch(wpJson).then(response => response.json()).then(data => markerMaker(data));



function markerMaker(data){
    data.forEach((item, index) => {
      const title = item.title.rendered;      
      const lat = item.lat;
      const long = item.lng;
      const marker = L.marker([lat, long]).addTo(map);
      let imgHtml = '';
      if(item.f_img){
        const imgUrl = item.f_img;
        imgHtml = `<img src="${imgUrl}" alt="Picture of the home town for ${title}" width="150px" height="auto">`;
      }
      if (lat != '' && long != ''){
        marker.bindPopup(`<h2 class="popup-name">${title}</h2> ${imgHtml}`);
      }
  });
}
