<footer class="footer">
    <div id="gm" style="height: 450px;"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCgqs_0_glakYRIvqjMSYoJIdeBHpV4tE0"></script>
    <script type="text/javascript">
        google.maps.event.addDomListener(window, 'load', init);
        function init() {
            var mapOptions = {
                zoom: 12,
                scrollwheel: true,
                center: new google.maps.LatLng(49.996192, 36.232122)
            };

            // Get the HTML DOM element that will contain your map
            // We are using a div with id="map" seen below in the <body>
            var mapElement = document.getElementById('gm');

            // Create the Google Map using our element and options defined above
            var map = new google.maps.Map(mapElement, mapOptions);

            // var image = new google.maps.MarkerImage('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAACXBIWXMAAAsTAAALEwEAmpwYAAA7WGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzggNzkuMTU5ODI0LCAyMDE2LzA5LzE0LTAxOjA5OjAxICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgICAgICAgICB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgICAgICAgICB4bWxuczpwaG90b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgICAgIDx4bXA6Q3JlYXRlRGF0ZT4yMDE4LTEwLTA5VDEyOjQxOjE4KzAzOjAwPC94bXA6Q3JlYXRlRGF0ZT4KICAgICAgICAgPHhtcDpNb2RpZnlEYXRlPjIwMTgtMTAtMTVUMTE6Mjk6MDErMDM6MDA8L3htcDpNb2RpZnlEYXRlPgogICAgICAgICA8eG1wOk1ldGFkYXRhRGF0ZT4yMDE4LTEwLTE1VDExOjI5OjAxKzAzOjAwPC94bXA6TWV0YWRhdGFEYXRlPgogICAgICAgICA8eG1wTU06SW5zdGFuY2VJRD54bXAuaWlkOjZkYWZkNTU2LTQwMWYtMWY0OC05ODk2LWZlNWEyZGVlOTdjMDwveG1wTU06SW5zdGFuY2VJRD4KICAgICAgICAgPHhtcE1NOkRvY3VtZW50SUQ+eG1wLmRpZDoyMjJFRkRENEM2MjYxMUU4OEVEMzg1RTg3MjhCNzk1MjwveG1wTU06RG9jdW1lbnRJRD4KICAgICAgICAgPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgPHN0UmVmOmluc3RhbmNlSUQ+eG1wLmlpZDoyMjJFRkREMUM2MjYxMUU4OEVEMzg1RTg3MjhCNzk1Mjwvc3RSZWY6aW5zdGFuY2VJRD4KICAgICAgICAgICAgPHN0UmVmOmRvY3VtZW50SUQ+eG1wLmRpZDoyMjJFRkREMkM2MjYxMUU4OEVEMzg1RTg3MjhCNzk1Mjwvc3RSZWY6ZG9jdW1lbnRJRD4KICAgICAgICAgPC94bXBNTTpEZXJpdmVkRnJvbT4KICAgICAgICAgPHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD54bXAuZGlkOjIyMkVGREQ0QzYyNjExRTg4RUQzODVFODcyOEI3OTUyPC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDo5ZDg4ZjVhYy00OTFjLTE2NDEtOThhNi05MTM5NDJlN2M1OGU8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTgtMTAtMTVUMTE6MTc6MTArMDM6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE3IChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6NmRhZmQ1NTYtNDAxZi0xZjQ4LTk4OTYtZmU1YTJkZWU5N2MwPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTEwLTE1VDExOjI5OjAxKzAzOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwveG1wTU06SGlzdG9yeT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6WFJlc29sdXRpb24+NzIwMDAwLzEwMDAwPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpZUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WVJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDxleGlmOkNvbG9yU3BhY2U+NjU1MzU8L2V4aWY6Q29sb3JTcGFjZT4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjEwPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxZRGltZW5zaW9uPjEwPC9leGlmOlBpeGVsWURpbWVuc2lvbj4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz6YYXuhAAAAIGNIUk0AAHolAACAgwAA+f8AAIDoAABSCAABFVgAADqXAAAXb9daH5AAAABzSURBVHjajM+xEYJQEEXRw8aWgwWQK9aCsaFEzEgvYk4B2I4zhCZLCv/OvGD33WC3WgcbV3Q457zghQ9ELp94o8Ep02DKTrUOLrnYow3cHdMF6gKxDmVUgW+BuATGAnGM/LjfkXpM240P3DDjl5nRZuc/ACNqFduQDc/6AAAAAElFTkSuQmCC',
            //     new google.maps.Size(10, 10),
            //     new google.maps.Point(0,0),
            //     new google.maps.Point(5, 5));
            // Let's also add a marker while we're at it
            var marker1 = new google.maps.Marker({
                position: new google.maps.LatLng(49.999068, 36.241342),
                map: map,
                // icon: image,
            });
            var marker2 = new google.maps.Marker({
                position: new google.maps.LatLng(49.993779, 36.228100),
                map: map,
                // icon: image,
            });
            var marker3 = new google.maps.Marker({
                position: new google.maps.LatLng(50.028493, 36.221931),
                map: map,
                // icon: image,
            });
            var marker4 = new google.maps.Marker({
                position: new google.maps.LatLng(49.990223, 36.263739),
                map: map,
                // icon: image,
            });
            var marker5 = new google.maps.Marker({
                position: new google.maps.LatLng(49.985182, 36.189962),
                map: map,
                // icon: image,
            });
            var marker6 = new google.maps.Marker({
                position: new google.maps.LatLng(50.024972, 36.334056),
                map: map,
                // icon: image,
            });
            var marker7 = new google.maps.Marker({
                position: new google.maps.LatLng(49.994029, 36.227414),
                map: map,
                // icon: image,
            });
            var marker8 = new google.maps.Marker({
                position: new google.maps.LatLng(49.957270, 36.359691),
                map: map,
                // icon: image,
            });
            var marker9 = new google.maps.Marker({
                position: new google.maps.LatLng(49.997021, 36.339234),
                map: map,
                // icon: image,
            });
            var marker10 = new google.maps.Marker({
                position: new google.maps.LatLng(49.943600, 36.301988),
                map: map,
                // icon: image,
            });


            var content1 = '<div id="iw-container">' +
                '<p class="iw-title">ул. Пушкинская, 43</p>' +
                '<p class="iw-info">(поликлиника "Cана" вход в арку)</p>' +
                '<a href="tel:+380577518345" class="iw-info">+380 (57) 751-83-45</a>' +
                '<img src="/images/addr/addr8.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content2 = '<div id="iw-container">' +
                '<p class="iw-title">Бурсацкий спуск, 8</p>' +
                '<a href="tel:+380577312674" class="iw-info">+380 (57) 731-26-74</a>' +
                '<img src="/images/addr/addr1.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content3 = '<div id="iw-container">' +
                '<p class="iw-title">пр. Ленина, 29</p>' +
                '<a href="tel:+380577195653" class="iw-info">+380 (57) 719-56-53</a>' +
                '</div>';
            var content4 = '<div id="iw-container">' +
                '<p class="iw-title">пр. Московский, 90</p>' +
                '<img src="/images/addr/addr4.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content5 = '<div id="iw-container">' +
                '<p class="iw-title">Полтавский Шлях 115</p>' +
                '<p class="iw-info">ст. м. «Холодная Гора»</p>' +
                '<p class="iw-info">(напротив ТЦ "РОСТ", рынок торгового центра Дигма)</p>' +
                '<img src="/images/addr/addr6.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content6 = '<div id="iw-container">' +
                '<p class="iw-title">ст. м. «Героев Труда»</p>' +
                '<p class="iw-info">трам. ост. Рынок «АРАКС»</p>' +
                '<img src="/images/addr/addr3.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content7 = '<div id="iw-container">' +
                '<p class="iw-title">ул. Клочковская</p>' +
                '<p class="iw-info">(напротив книжного рынка)</p>' +
                '<img src="/images/addr/addr2.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content8 = '<div id="iw-container">' +
                '<p class="iw-title">пр. Московский, 252б</p>' +
                '<p class="iw-info">(станция метро им. Масельского)</p>' +
                '<img src="/images/addr/addr5.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content9 = '<div id="iw-container">' +
                '<p class="iw-title">пр. Тракторостроителей, 63</p>' +
                '<p class="iw-info">(Напротив ТРЦ "Украина")</p>' +
                '<img src="/images/addr/addr7.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content10 = '<div id="iw-container">' +
                '<p class="iw-title">Героев Сталинграда 136/8</p>' +
                '<p class="iw-info">торговое помещение 94/1</p>' +
                '<img src="/images/addr/addr10.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';

            // A new Info Window is created and set content
            var infowindow1 = new google.maps.InfoWindow({
                content: content1,
                // Assign a maximum value for the width of the infowindow allows
                // greater control over the various content elements
                maxWidth: 300
            });
            var infowindow2 = new google.maps.InfoWindow({
                content: content2,
                maxWidth: 300
            });
            var infowindow3 = new google.maps.InfoWindow({
                content: content3,
                maxWidth: 300
            });
            var infowindow4 = new google.maps.InfoWindow({
                content: content4,
                maxWidth: 300
            });
            var infowindow5 = new google.maps.InfoWindow({
                content: content5,
                maxWidth: 300
            });
            var infowindow6 = new google.maps.InfoWindow({
                content: content6,
                maxWidth: 300
            });
            var infowindow7 = new google.maps.InfoWindow({
                content: content7,
                maxWidth: 300
            });
            var infowindow8 = new google.maps.InfoWindow({
                content: content8,
                maxWidth: 300
            });
            var infowindow9 = new google.maps.InfoWindow({
                content: content9,
                maxWidth: 300
            });
            var infowindow10 = new google.maps.InfoWindow({
                content: content10,
                maxWidth: 300
            });

            // This event expects a click on a marker
            // When this event is fired the Info Window is opened.
            google.maps.event.addListener(marker1, 'click', function() {
                infowindow1.open(map,marker1);
            });
            google.maps.event.addListener(marker2, 'click', function() {
                infowindow2.open(map,marker2);
            });
            google.maps.event.addListener(marker3, 'click', function() {
                infowindow3.open(map,marker3);
            });
            google.maps.event.addListener(marker4, 'click', function() {
                infowindow4.open(map,marker4);
            });
            google.maps.event.addListener(marker5, 'click', function() {
                infowindow5.open(map,marker5);
            });
            google.maps.event.addListener(marker6, 'click', function() {
                infowindow6.open(map,marker6);
            });
            google.maps.event.addListener(marker7, 'click', function() {
                infowindow7.open(map,marker7);
            });
            google.maps.event.addListener(marker8, 'click', function() {
                infowindow8.open(map,marker8);
            });
            google.maps.event.addListener(marker9, 'click', function() {
                infowindow9.open(map,marker9);
            });
            google.maps.event.addListener(marker10, 'click', function() {
                infowindow10.open(map,marker10);
            });

            // Event that closes the Info Window with a click on the map
            google.maps.event.addListener(map, 'click', function() {
                infowindow1.close();
                infowindow2.close();
                infowindow3.close();
                infowindow4.close();
                infowindow5.close();
                infowindow6.close();
                infowindow7.close();
                infowindow8.close();
                infowindow9.close();
                infowindow10.close();
            });

            var mapElement1 = document.getElementById('addr1');
            if(mapElement1 !== null){
                var map1 = new google.maps.Map(mapElement1, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.999068, 36.241342)
                });
                var marker1 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.999068, 36.241342),
                    map: map1,
                });
                google.maps.event.addListener(marker1, 'click', function() {
                    infowindow1.open(map1,marker1);
                });
                google.maps.event.addListener(map1, 'click', function() {
                    infowindow1.close();
                });
            }

            var mapElement2 = document.getElementById('addr2');
            if(mapElement2 !== null){
                var map2 = new google.maps.Map(mapElement2, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.993779, 36.228100)
                });
                var marker2 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.993779, 36.228100),
                    map: map2,
                });
                google.maps.event.addListener(marker2, 'click', function() {
                    infowindow2.open(map2,marker2);
                });
                google.maps.event.addListener(map2, 'click', function() {
                    infowindow2.close();
                });
            }

            var mapElement3 = document.getElementById('addr3');
            if(mapElement3 !== null){
                var map3 = new google.maps.Map(mapElement3, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(50.028493, 36.221931)
                });
                var marker3 = new google.maps.Marker({
                    position: new google.maps.LatLng(50.028493, 36.221931),
                    map: map3,
                });
                google.maps.event.addListener(marker3, 'click', function() {
                    infowindow3.open(map3,marker3);
                });
                google.maps.event.addListener(map3, 'click', function() {
                    infowindow3.close();
                });
            }

            var mapElement4 = document.getElementById('addr4');
            if(mapElement4 !== null){
                var map4 = new google.maps.Map(mapElement4, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.990223, 36.263739)
                });
                var marker4 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.990223, 36.263739),
                    map: map4,
                });
                google.maps.event.addListener(marker4, 'click', function() {
                    infowindow4.open(map4,marker4);
                });
                google.maps.event.addListener(map4, 'click', function() {
                    infowindow4.close();
                });
            }

            var mapElement5 = document.getElementById('addr5');
            if(mapElement5 !== null){
                var map5 = new google.maps.Map(mapElement5, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.985182, 36.189962)
                });
                var marker5 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.985182, 36.189962),
                    map: map5,
                });
                google.maps.event.addListener(marker5, 'click', function() {
                    infowindow5.open(map5,marker5);
                });
                google.maps.event.addListener(map5, 'click', function() {
                    infowindow5.close();
                });
            }

            var mapElement6 = document.getElementById('addr6');
            if(mapElement6 !== null){
                var map6 = new google.maps.Map(mapElement6, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(50.024972, 36.334056)
                });
                var marker6 = new google.maps.Marker({
                    position: new google.maps.LatLng(50.024972, 36.334056),
                    map: map6,
                });
                google.maps.event.addListener(marker6, 'click', function() {
                    infowindow6.open(map6,marker6);
                });
                google.maps.event.addListener(map6, 'click', function() {
                    infowindow6.close();
                });
            }

            var mapElement7 = document.getElementById('addr7');
            if(mapElement7 !== null){
                var map7 = new google.maps.Map(mapElement7, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.994029, 36.227414)
                });
                var marker7 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.994029, 36.227414),
                    map: map7,
                });
                google.maps.event.addListener(marker7, 'click', function() {
                    infowindow7.open(map7,marker7);
                });
                google.maps.event.addListener(map7, 'click', function() {
                    infowindow7.close();
                });
            }

            var mapElement8 = document.getElementById('addr8');
            if(mapElement8 !== null){
                var map8 = new google.maps.Map(mapElement8, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.957270, 36.359691)
                });
                var marker8 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.957270, 36.359691),
                    map: map8,
                });
                google.maps.event.addListener(marker8, 'click', function() {
                    infowindow8.open(map8,marker8);
                });
                google.maps.event.addListener(map8, 'click', function() {
                    infowindow8.close();
                });
            }

            var mapElement9 = document.getElementById('addr9');
            if(mapElement9 !== null){
                var map9 = new google.maps.Map(mapElement9, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.997021, 36.339234)
                });
                var marker9 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.997021, 36.339234),
                    map: map9,
                });
                google.maps.event.addListener(marker9, 'click', function() {
                    infowindow9.open(map9,marker9);
                });
                google.maps.event.addListener(map9, 'click', function() {
                    infowindow9.close();
                });
            }

            var mapElement10 = document.getElementById('addr10');
            if(mapElement10 !== null){
                var map10 = new google.maps.Map(mapElement10, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(49.943600, 36.301988)
                });
                var marker10 = new google.maps.Marker({
                    position: new google.maps.LatLng(49.943600, 36.301988),
                    map: map10,
                });
                google.maps.event.addListener(marker10, 'click', function() {
                    infowindow10.open(map10,marker10);
                });
                google.maps.event.addListener(map10, 'click', function() {
                    infowindow10.close();
                });
            }

            var mapMuzElement = document.getElementById('map');
            if(mapMuzElement !== null){
                var mapMuz = new google.maps.Map(mapMuzElement, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(50.010519, 36.242132)
                });
                var markerMuz = new google.maps.Marker({
                    position: new google.maps.LatLng(50.010519, 36.242132),
                    map: mapMuz,
                });
                var contentMuz = '<div id="iw-container">' +
                    '<p class="iw-title">ул. Маяковского, 5 (во двор)</p>' +
                    '<a href="+380577156315" class="iw-info">+38 (057) 715-63-15</a>' +
                    '<a href="+380577005002" class="iw-info">+38 (057) 700-50-02</a>' +
                    '</div>';
                var infowindowMuz = new google.maps.InfoWindow({
                    content: contentMuz,
                    maxWidth: 300
                });
                google.maps.event.addListener(markerMuz, 'click', function() {
                    infowindowMuz.open(mapMuz,markerMuz);
                });
                google.maps.event.addListener(mapMuz, 'click', function() {
                    infowindowMuz.close();
                });
            }
        }

    </script>

    {{--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2562.3256914431086!2d36.21304241599528!3d50.042729879421096!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4127a6ae15c37805%3A0x4356917fd8cbf4bf!2z0L_RgNC-0YHQv9C10LrRgiDQm9C10L3RltC90LAsIDI5LCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGMLCA2MTAwMA!5e0!3m2!1sru!2sua!4v1539860122128" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
    <div class="map-form-container">
        <form action="/sendmail" class="question-popup__form ajax_form"
                data-error-title="Ошибка отправки!"
                data-error-message="Попробуйте отправить вопрос через некоторое время."
                data-success-title="Спасибо за вопрос!"
                data-success-message="Наш менеджер свяжется с вами в ближайшее время.">
            <textarea name="request" placeholder="Напишите свой вопрос" data-validate-required="Обязательное поле" data-title="Вопрос"></textarea>
            <input type="tel" name="phone" placeholder="Номер телефона" data-title="Телефон" data-validate-required="Обязательное поле" data-validate-uaphone="Неправильный номер">
            <button type="submit">Задать анонимный вопрос</button>
        </form>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-2 col-xs-3">
                @if(Request::path()!='/')
                    <a href="{{env('APP_URL')}}">
                        <img src="/images/logo.png" class="header__logo" alt="Главная">
                    </a>
                @else
                    <img src="/images/logo.png" class="header__logo" alt="Главная">
                @endif
            </div>
            <div class="col-sm-7 col-xs-9">
                <p class="copyright">© 2007-{{ date('Y') }} Интернет-магазин «Интим»</p>
            </div>
            <div class="col-sm-3 col-xs-12">
                <ul class="footer__contacts">
                    <li><a href="tel:0509712569" style="color: #fff;">050 971-25-69</a></li>
                    <li><a href="tel:0958860978" style="color: #fff;">095 886-09-78</a></li>
                    <li>shop_sex.com.ua</li>
                </ul>
            </div>
        </div>
    </div>
</footer>