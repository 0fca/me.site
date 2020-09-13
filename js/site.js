document.getElementById("date").innerText = new Date().getFullYear().toString();

document.addEventListener('DOMContentLoaded', function() {
  let elems = document.querySelectorAll('.slider');
  let options = {"interval" : 10000};
  let instances = M.Slider.init(elems, options);
});
