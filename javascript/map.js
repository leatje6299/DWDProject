
gsap.registerPlugin(Draggable);

var container = document.getElementById("container");

function update() {
    Draggable.create(".map", {
        bounds: container,
        edgeResistance: 0.1,
        type: "x,y",
        throwProps: true,
        autoScroll: true,
        zIndexBoost: false,
        zIndex: -1
    });
}

function openModal(name) {
    console.log("clicked on" + name);
    document.getElementById('modal-content').textContent = name;
    document.getElementById('modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
document.addEventListener("DOMContentLoaded", function () {
    update();
});