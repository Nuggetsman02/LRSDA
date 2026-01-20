$(function () {
    console.log("Test script loaded successfully.");
    $.get("/test", function (data) {
        console.log("Received data:", data);
        $(".test-message").text(data.message);
    });
});