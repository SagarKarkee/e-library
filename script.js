let usernav=document.querySelector('.user_header .header_1 .user_flex .navbar');

document.getElementById('user_menu_btn').onclick=()=>{
  usernav.classList.toggle('active');
  accbox.classList.remove('active');
};

let accbox = document.querySelector('.header_acc_box');
document.getElementById('user_btn').onclick = () => { 
  accbox.classList.toggle('active');
  usernav.classList.remove('active');
};

window.onscroll = () => {
  accbox.classList.remove('active');
  usernav.classList.remove('active');
  let nav = document.querySelector('.user_header .header_1');

  if (window.scrollY > 70) {
    nav.classList.add('active');
  } else {
    nav.classList.remove('active');
  }
};


document.addEventListener('DOMContentLoaded', function() {
  // Handle video interactions
  document.querySelectorAll('.media-item').forEach(item => {
      const video = item.querySelector('video');
      const thumbnail = item.querySelector('.media-thumbnail');

      if (video) {
          // Play/pause on click
          item.addEventListener('click', function(e) {
              if (video.paused) {
                  video.play();
                  video.classList.add('playing');
              } else {
                  video.pause();
                  video.classList.remove('playing');
              }
          });

          // Handle video states
          video.addEventListener('play', () => {
              video.classList.add('playing');
              thumbnail.style.opacity = 0;
          });

          video.addEventListener('pause', () => {
              video.classList.remove('playing');
              if (!item.matches(':hover')) {
                  thumbnail.style.opacity = 1;
              }
          });

          video.addEventListener('ended', () => {
              video.classList.remove('playing');
              thumbnail.style.opacity = 1;
          });
      }
  });
});
