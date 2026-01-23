// $(function () {
//     console.log("Test script loaded successfully.");
//     $.get("/test", function (data) {
//         console.log("Received data:", data);
//         $(".test-message").text(data.message);
//     });
// });
$(function () {
const payload = {
    filters : ["user1", "actionA"],
};
$.get("/test", payload).then(function (data) {

        let tab = $(".statement-tab");
        let table = $("<table></table>");
        let thead = $("<thead></thead>");
        let tbody = $("<tbody></tbody>");

        // En-têtes
        let headers = ["User", "Action", "Object", "Score", "Timestamp"];
        let headRow = $("<tr></tr>");

        $.each(headers, function (_, h) {
            headRow.append($("<th></th>").text(h));
        });

        thead.append(headRow);

        // Lignes de données
        $.each(data, function (_, value) {
            var row = $("<tr></tr>");

            row.append($("<td></td>").text(value.user));
            row.append($("<td></td>").text(value.action));
            row.append($("<td></td>").text(value.object));
            row.append($("<td></td>").text(value.score));
            row.append($("<td></td>").text(value.timestamp));

            tbody.append(row);
        });

        table.append(thead);
        table.append(tbody);
        tab.empty().append(table);
    });
});