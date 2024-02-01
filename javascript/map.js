
gsap.registerPlugin(Draggable);

var container = document.getElementById("container");

function update() {
    console.log("hi i am called");
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
console.log("HI");

document.addEventListener("DOMContentLoaded", function () {
    update();
});