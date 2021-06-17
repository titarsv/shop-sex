<footer class="footer">
    <div id="gm" style="height: 450px;"></div>
    @if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false && config('app.debug') === false)
    {{--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAdZiyAi-wihFqIfVhsaH6TtQXInoNtS4"></script>
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
            var marker11 = new google.maps.Marker({
                position: new google.maps.LatLng(50.058784, 36.201487),
                map: map,
                // icon: image,
            });
            var marker12 = new google.maps.Marker({
                position: new google.maps.LatLng(50.001949, 36.271176),
                map: map,
                // icon: image,
            });


            var content1 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.st_pushkinskaya_43') }}</p>' +
                '<p class="iw-info">{{ trans('app.polyclinic_sana_entrance_to_the_arch') }}</p>' +
                '<a href="tel:+380577518345" class="iw-info">+380 (57) 751-83-45</a>' +
                '<img src="/images/addr/addr8.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content2 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.bursatsky_descent_8') }}</p>' +
                '<a href="tel:+380577312674" class="iw-info">+380 (57) 731-26-74</a>' +
                '<img src="/images/addr/addr1.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content3 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.lenin_ave_29') }}</p>' +
                '<a href="tel:+380577195653" class="iw-info">+380 (57) 719-56-53</a>' +
                '</div>';
            var content4 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.moskovsky_pr_90') }}</p>' +
                '<img src="/images/addr/addr4.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content5 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.poltavskiy_shlyakh_115') }}</p>' +
                '<p class="iw-info">{{ trans('app.art_m_cold_mountain') }}</p>' +
                '<p class="iw-info">{{ trans('app.opposite_the_rost_shopping_center_the_market_of_the_digma_shopping_center') }}</p>' +
                '<img src="/images/addr/addr6.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content6 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.art_m_heroes_of_labor') }}</p>' +
                '<p class="iw-info">{{ trans('app.tram_ost_market_arax') }}</p>' +
                '<img src="/images/addr/addr3.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content7 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.st_klochkovskaya') }}</p>' +
                '<p class="iw-info">{{ trans('app.opposite_the_book_market') }}</p>' +
                '<img src="/images/addr/addr2.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content8 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.moskovsky_pr_252b') }}</p>' +
                '<p class="iw-info">{{ trans('app.metro_station_named_after_maselsky') }}</p>' +
                '<img src="/images/addr/addr5.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content9 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.ave_traktorostroiteley_63') }}</p>' +
                '<p class="iw-info">{{ trans('app.opposite_the_shopping_center_ukraine') }}</p>' +
                '<img src="/images/addr/addr7.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content10 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.heroes_of_stalingrad_1368') }}</p>' +
                '<p class="iw-info">{{ trans('app.retail_space_941') }}</p>' +
                '<img src="/images/addr/addr10.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content11 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.ludwig_svoboda_ave_43') }}</p>'+
                '<img src="/uploads/addr11.jpg" class="iw-info" style="width: 283px;">' +
                '</div>';
            var content12 = '<div id="iw-container">' +
                '<p class="iw-title">{{ trans('app.st_shevchenko_142a') }}</p>'+
                '<p class="iw-info">{{ trans('app.shopping_center_rost_metro_station_kievskaya') }}</p>' +
                '<img src="/uploads/addr12.jpg" class="iw-info" style="width: 283px;">' +
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
            var infowindow11 = new google.maps.InfoWindow({
                content: content11,
                maxWidth: 300
            });
            var infowindow12 = new google.maps.InfoWindow({
                content: content12,
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
            google.maps.event.addListener(marker11, 'click', function() {
                infowindow11.open(map,marker11);
            });
            google.maps.event.addListener(marker12, 'click', function() {
                infowindow12.open(map,marker12);
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
                infowindow11.close();
                infowindow12.close();
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

            var mapElement11 = document.getElementById('addr11');
            if(mapElement11 !== null){
                var map11 = new google.maps.Map(mapElement11, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(50.058784, 36.201487)
                });
                var marker11 = new google.maps.Marker({
                    position: new google.maps.LatLng(50.058784, 36.201487),
                    map: map11,
                });
                google.maps.event.addListener(marker11, 'click', function() {
                    infowindow11.open(map11,marker11);
                });
                google.maps.event.addListener(map11, 'click', function() {
                    infowindow11.close();
                });
            }

            var mapElement12 = document.getElementById('addr12');
            if(mapElement12 !== null){
                var map12 = new google.maps.Map(mapElement12, {
                    zoom: 16,
                    scrollwheel: true,
                    center: new google.maps.LatLng(50.001949, 36.271176)
                });
                var marker12 = new google.maps.Marker({
                    position: new google.maps.LatLng(50.001949, 36.271176),
                    map: map12,
                });
                google.maps.event.addListener(marker12, 'click', function() {
                    infowindow12.open(map12,marker12);
                });
                google.maps.event.addListener(map12, 'click', function() {
                    infowindow12.close();
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
                    '<p class="iw-title">{{ trans('app.st_mayakovsky_5_in_the_yard') }}</p>' +
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
    </script>--}}
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2564.5157367630272!2d36.26932921600078!3d50.00168797941599!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4127a0b9d24040e1%3A0xea0df70e6ad4c5e6!2z0KDQvtGB0YIg0JrQuNC10LLRgdC60LjQuQ!5e0!3m2!1sru!2sua!4v1623920141810!5m2!1sru!2sua" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    {{--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2562.3256914431086!2d36.21304241599528!3d50.042729879421096!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4127a6ae15c37805%3A0x4356917fd8cbf4bf!2z0L_RgNC-0YHQv9C10LrRgiDQm9C10L3RltC90LAsIDI5LCDQpdCw0YDQutGW0LIsINCl0LDRgNC60ZbQstGB0YzQutCwINC-0LHQu9Cw0YHRgtGMLCA2MTAwMA!5e0!3m2!1sru!2sua!4v1539860122128" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
    @endif
    <div class="map-form-container">
        <form action="/sendmail" class="question-popup__form ajax_form"
                data-error-title="{{ trans('app.send_error') }}"
                data-error-message="{{ trans('app.try_to_send_a_question_after_a_while') }}"
                data-success-title="{{ trans('app.thanks_for_the_question') }}"
                data-success-message="{{ trans('app.our_manager_will_contact_you_soon') }}">
            <textarea name="request" placeholder="{{ trans('app.write_your_question') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-title="{{ trans('app.question') }}"></textarea>
            <input type="tel" name="phone" placeholder="{{ trans('app.phone_number') }}" data-title="{{ trans('app.phone') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-validate-uaphone="{{ trans('app.wrong_number') }}">
            <button type="submit">{{ trans('app.ask_anonymous_question') }}</button>
        </form>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-2 col-xs-3">
                @if(Request::path()!='/')
                    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
                        {{--<img src="/images/logo.png" class="header__logo" alt="{{ trans('app.home') }}">--}}
                        <picture>
                            <source data-src="/images/logo.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/logo.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="{{ trans('app.home') }}" style="width: 100%;">
                        </picture>
                    </a>
                @else
                    {{--<img src="/images/logo.png" class="header__logo" alt="{{ trans('app.home') }}">--}}
                    <picture>
                        <source data-src="/images/logo.webp" srcset="/images/pixel.webp" type="image/webp">
                        <source data-src="/images/logo.png" srcset="/images/pixel.png" type="image/png">
                        <img src="/images/pixel.jpg" alt="{{ trans('app.home') }}" style="width: 100%;">
                    </picture>
                @endif
            </div>
            <div class="col-sm-7 col-xs-9">
                <p class="copyright">© 2007-{{ date('Y') }} {{ trans('app.internet_store_intim') }}</p>
            </div>
            <div class="col-sm-3 col-xs-12">
                <ul class="footer__contacts">
                    <li style="cursor: pointer;" class="popup-btn" data-mfp-src="#popup-return">{{ trans('app.exchange_return_conditions') }}</li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/oferta/" style="color: #fff;">{{ trans('app.offer') }}</a></li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/politika-konfidentsialnosti" style="color: #fff;">{{ trans('app.privacy_policy') }}</a></li>
                    <li><a href="tel:0507000197" style="color: #fff;">050 700-01-97</a></li>
                    <li><a href="tel:0958860978" style="color: #fff;">095 886-09-78</a></li>
                    <li>shop_sex.com.ua</li>
                </ul>
            </div>
        </div>
    </div>
    <div style="padding: 25px 0 0;text-align: center;">
        <a href="https://triplefork.it" target="_blank" rel="nofollow" class="triplefork" style="display: flex; align-items: center;justify-content: center;margin: 0 auto;">
<span>{{ trans('app.development') }}</span>
<svg class="triplefork-logo" version="1.1" id="sl_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 27 25" style="enable-background:new 0 0 27 25;width: 26px;margin-right: 5px" xml:space="preserve">
<style type="text/css">
  .st0{fill:none;stroke:#F25733;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}  
</style>
<path class="st0" d="M12.9,10l9.7-4.3c0.5-0.2,0.8-0.3,1-0.2C23.8,5.6,24,5.8,24,6.1c0,0.3-0.1,1.8-0.3,2.1c-0.2,0.3-0.6,0.6-1,0.8
  l-7.8,3.4v2.4l4.8-2.2c0.4-0.2,0.8-0.3,1-0.2c0.2,0.1,0.3,0.3,0.3,0.6c0,0.3-0.1,1.8-0.3,2.1c-0.2,0.3-0.5,0.5-1,0.7L14.8,18v2.3"></path>
<path class="st0" d="M23.6,14.6c0,0,0,3.5,0,3.6c0,0.4-0.1,0.6-0.5,0.9c-0.1,0.1-0.2,0.1-0.4,0.2c-2.5,1.4-9.3,4.5-9.5,4.6
  c-0.4,0.2-0.9,0.1-1.1,0c-0.4-0.2-0.7-0.3-0.9-0.7C10.9,22.7,11,22,11,21.4v-4.5v-2.1v-2.3L3.3,8.8c-0.5-0.2-0.8-0.5-1-0.8
  C2,7.8,1.9,6.2,1.9,5.9c0-0.3,0.1-0.5,0.3-0.6c0.2-0.1,0.6,0,1,0.2l9.6,4.5"></path>
<path class="st0" d="M7.5,21.8c-0.3-0.1-4.4-2-4.5-2.2c-0.6-0.4-0.7-0.6-0.7-1.1c0-0.1-0.2-6.2-0.2-6.2"></path>
<path class="st0" d="M6.7,3.1C6.9,3,12,0.9,12,0.9c0.9-0.2,0.9-0.2,1.7,0c0,0,5.4,2,5.4,2"></path>
</svg>
<svg class="triplefork-txt" version="1.1" id="sl_2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 98 15" style="enable-background:new 0 0 98 15;width: 97px;" xml:space="preserve">
<style type="text/css">
  .st1{fill:#fff;stroke:#fff;}
</style>
<path class="st1" d="M9,0.4c0.1,0.1,0.2,0.3,0.2,0.5c0,0.2-0.1,0.4-0.2,0.5C8.9,1.5,8.7,1.6,8.5,1.6H5.4v8.9c0,0.2-0.1,0.4-0.2,0.5
  c-0.1,0.1-0.3,0.2-0.5,0.2c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V1.6H0.7c-0.2,0-0.4-0.1-0.5-0.2C0.1,1.3,0,1.1,0,0.9
  c0-0.2,0.1-0.4,0.2-0.5c0.1-0.1,0.3-0.2,0.5-0.2h7.8C8.7,0.2,8.9,0.3,9,0.4z"></path>
<path class="st1" d="M13.9,3.7c0.3,0.2,0.4,0.3,0.4,0.6c0,0.1,0,0.1,0,0.1c-0.1,0.3-0.3,0.4-0.6,0.4c0,0-0.1,0-0.2,0
  c-0.4-0.1-0.7-0.1-1-0.1c-0.7,0-1.3,0.2-1.7,0.5c-0.4,0.3-0.6,0.7-0.6,1.2v4.1c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.6,0.2
  c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V4.2c0-0.2,0.1-0.4,0.2-0.5C9,3.6,9.2,3.5,9.4,3.5c0.5,0,0.7,0.2,0.7,0.7v0.3
  c0.3-0.3,0.7-0.6,1.1-0.8c0.4-0.2,0.9-0.3,1.4-0.3C13.2,3.4,13.6,3.5,13.9,3.7z"></path>
<path class="st1" d="M17.4,0.7c0.2,0.2,0.3,0.4,0.3,0.7c0,0.3-0.1,0.5-0.3,0.7c-0.2,0.2-0.4,0.3-0.7,0.3c-0.3,0-0.5-0.1-0.7-0.3
  c-0.2-0.2-0.3-0.4-0.3-0.7c0-0.3,0.1-0.5,0.3-0.7c0.2-0.2,0.4-0.3,0.7-0.3C17,0.4,17.2,0.5,17.4,0.7z M16.2,3.7
  c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.4,0.1,0.5,0.2c0.1,0.1,0.2,0.3,0.2,0.5v6.2c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2
  c-0.2,0-0.4-0.1-0.5-0.2C16,10.9,16,10.7,16,10.5V4.2C16,4,16,3.8,16.2,3.7z"></path>
<path class="st1" d="M26.3,4c0.6,0.3,1.1,0.8,1.5,1.4c0.4,0.6,0.5,1.3,0.5,2c0,0.7-0.2,1.4-0.5,2c-0.3,0.6-0.8,1.1-1.3,1.4
  c-0.6,0.3-1.2,0.5-1.9,0.5c-0.6,0-1.1-0.1-1.6-0.3c-0.5-0.2-0.9-0.5-1.2-0.9v3.8c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2
  c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V7.4c0-0.7,0.2-1.4,0.5-2c0.4-0.6,0.8-1.1,1.4-1.4c0.6-0.3,1.3-0.5,2-0.5
  C25,3.5,25.7,3.6,26.3,4z M25.6,9.6c0.4-0.2,0.7-0.5,0.9-0.9c0.2-0.4,0.3-0.8,0.3-1.3S26.8,6.4,26.5,6S26,5.3,25.6,5.1
  c-0.4-0.2-0.8-0.3-1.3-0.3c-0.5,0-0.9,0.1-1.3,0.3C22.6,5.3,22.2,5.6,22,6c-0.2,0.4-0.3,0.9-0.3,1.3c0,0.5,0.1,0.9,0.3,1.3
  c0.2,0.4,0.5,0.7,0.9,0.9c0.4,0.2,0.8,0.3,1.3,0.3C24.8,10,25.2,9.9,25.6,9.6z"></path>
<path class="st1" d="M30.4,0.4c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.4,0.1,0.5,0.2c0.1,0.1,0.2,0.3,0.2,0.5v7.7c0,0.3,0.1,0.6,0.2,0.9
  c0.1,0.2,0.3,0.3,0.5,0.3h0.4c0.2,0,0.4,0.1,0.5,0.2c0.1,0.1,0.2,0.3,0.2,0.5c0,0.2-0.1,0.4-0.3,0.5c-0.2,0.1-0.4,0.2-0.7,0.2h0
  c-0.4,0-0.8-0.1-1.1-0.3c-0.3-0.2-0.6-0.5-0.8-0.9c-0.2-0.4-0.3-0.8-0.3-1.3V0.9C30.2,0.7,30.3,0.6,30.4,0.4z"></path>
<path class="st1" d="M42.6,7.7c-0.1,0.1-0.3,0.2-0.5,0.2h-5.6c0.1,0.7,0.4,1.2,0.9,1.6C38,9.8,38.6,10,39.3,10
  c0.3,0,0.6-0.1,0.9-0.2c0.3-0.1,0.6-0.2,0.8-0.4c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.3,0,0.4,0.1c0.2,0.1,0.3,0.3,0.3,0.5
  c0,0.2-0.1,0.3-0.2,0.4c-0.3,0.3-0.7,0.5-1.2,0.6c-0.5,0.2-1,0.2-1.4,0.2c-0.8,0-1.5-0.2-2.2-0.5c-0.6-0.3-1.1-0.8-1.5-1.4
  c-0.4-0.6-0.5-1.3-0.5-2c0-0.7,0.2-1.4,0.5-2C36,4.8,36.5,4.3,37.1,4c0.6-0.3,1.3-0.5,2-0.5c0.8,0,1.4,0.2,1.9,0.5
  c0.5,0.3,1,0.8,1.3,1.3c0.3,0.6,0.4,1.2,0.4,2C42.8,7.4,42.7,7.6,42.6,7.7z M37.4,5.2c-0.4,0.4-0.7,0.9-0.8,1.5h4.9
  c-0.1-0.6-0.3-1.1-0.7-1.5c-0.4-0.4-0.9-0.6-1.6-0.6C38.4,4.7,37.8,4.9,37.4,5.2z"></path>
<path class="st1" d="M51.2,5.2c0.1,0.1,0.2,0.3,0.2,0.5c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2h-4.2v4.1
  c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V1c0-0.2,0.1-0.4,0.2-0.5
  c0.1-0.1,0.3-0.2,0.5-0.2h6.6c0.2,0,0.4,0.1,0.5,0.2c0.1,0.1,0.2,0.3,0.2,0.5c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2h-5.9
  V5h4.2C50.9,5,51.1,5.1,51.2,5.2z"></path>
<path class="st1" d="M60.6,4c0.6,0.3,1.1,0.8,1.4,1.4c0.3,0.6,0.5,1.3,0.5,2c0,0.7-0.2,1.4-0.5,2c-0.3,0.6-0.8,1-1.4,1.4
  c-0.6,0.3-1.3,0.5-2.1,0.5c-0.8,0-1.5-0.2-2.1-0.5c-0.6-0.3-1.1-0.8-1.4-1.4c-0.3-0.6-0.5-1.3-0.5-2c0-0.8,0.2-1.4,0.5-2
  c0.3-0.6,0.8-1,1.4-1.4c0.6-0.3,1.3-0.5,2.1-0.5C59.3,3.5,60,3.6,60.6,4z M57.2,5.1c-0.4,0.2-0.7,0.5-0.9,0.9
  c-0.2,0.4-0.3,0.9-0.3,1.4c0,0.5,0.1,1,0.3,1.4c0.2,0.4,0.5,0.7,0.9,0.9C57.6,9.9,58,10,58.5,10c0.5,0,0.9-0.1,1.3-0.3
  c0.4-0.2,0.7-0.5,0.9-0.9c0.2-0.4,0.3-0.8,0.3-1.4S61,6.4,60.8,6c-0.2-0.4-0.5-0.7-0.9-0.9c-0.4-0.2-0.8-0.3-1.3-0.3
  C58,4.7,57.6,4.8,57.2,5.1z"></path>
<path class="st1" d="M69.9,3.7c0.3,0.2,0.4,0.3,0.4,0.6c0,0.1,0,0.1,0,0.1c-0.1,0.3-0.3,0.4-0.6,0.4c0,0-0.1,0-0.2,0
  c-0.4-0.1-0.7-0.1-1-0.1c-0.7,0-1.3,0.2-1.7,0.5c-0.4,0.3-0.6,0.7-0.6,1.2v4.1c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.6,0.2
  c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V4.2c0-0.2,0.1-0.4,0.2-0.5c0.1-0.1,0.3-0.2,0.5-0.2c0.5,0,0.7,0.2,0.7,0.7v0.3
  c0.3-0.3,0.7-0.6,1.1-0.8c0.4-0.2,0.9-0.3,1.4-0.3C69.2,3.4,69.6,3.5,69.9,3.7z"></path>
<path class="st1" d="M78.1,10.6c0,0.2-0.1,0.4-0.3,0.5c-0.1,0.1-0.3,0.2-0.5,0.2c-0.2,0-0.4-0.1-0.5-0.3l-2.8-3.3l-0.7,0.6v2.2
  c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V0.9c0-0.2,0.1-0.4,0.2-0.5
  c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.4,0.1,0.5,0.2c0.1,0.1,0.2,0.3,0.2,0.5v5.7l3.6-3c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.4,0.1,0.5,0.2
  C78,3.7,78,3.9,78,4.1c0,0.2-0.1,0.4-0.2,0.5l-2.7,2.3l2.8,3.3C78,10.3,78.1,10.4,78.1,10.6z"></path>
<path class="st1" d="M84.9,0.4c0.1-0.1,0.3-0.2,0.5-0.2c0.2,0,0.4,0.1,0.6,0.2c0.1,0.1,0.2,0.3,0.2,0.5v9.5c0,0.2-0.1,0.4-0.2,0.5
  c-0.1,0.1-0.3,0.2-0.6,0.2c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V1C84.6,0.8,84.7,0.6,84.9,0.4z"></path>
<path class="st1" d="M96.9,0.4c0.1,0.1,0.2,0.3,0.2,0.5c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2h-3.1v8.9
  c0,0.2-0.1,0.4-0.2,0.5c-0.1,0.1-0.3,0.2-0.5,0.2c-0.2,0-0.4-0.1-0.5-0.2c-0.1-0.1-0.2-0.3-0.2-0.5V1.6h-3.2c-0.2,0-0.4-0.1-0.5-0.2
  c-0.1-0.1-0.2-0.3-0.2-0.5c0-0.2,0.1-0.4,0.2-0.5c0.1-0.1,0.3-0.2,0.5-0.2h7.8C96.6,0.2,96.8,0.3,96.9,0.4z"></path>
</svg>
</a>
    </div>
</footer>
<div class="mfp-hide">
    <div id="popup-return" class="view-popup">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                    <div class="question-popup__container">
                        <p class="question-popup__container-title">{{ trans('app.exchange_return_conditions') }}</p>
                        <p>{{ trans('app.according_to_addendum_no_3_to_the_resolution_of_the_cabinet_of_ministers_of_ukraine_dated_march_19_1994_no_172') }}</p>
                        <br>
                        <p>{{ trans('app.hygiene_products_clothing_and_sex_products_of_proper_quality_cannot_be_exchanged__returned') }}</p>
                        <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>