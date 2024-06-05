/**
 * @class SocialLinks
 * @extends HTMLElement
 * @description Custom element to render social media links
 * @example
 * <social-links social_links='[{"name": "twitter", "url": "https://twitter.com/pascualmg"}]'></social-links>
 */
class SocialLinks extends HTMLElement {
    connectedCallback() {
        this.attachShadow({ mode: 'open' });

        const socialLinks = this.getAttribute('links') || '[]';
        const links = JSON.parse(socialLinks);
        this.assertFields(links);

        const rssIcon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBVcGxvYWRlZCB0bzogU1ZHIFJlcG8sIHd3dy5zdmdyZXBvLmNvbSwgR2VuZXJhdG9yOiBTVkcgUmVwbyBNaXhlciBUb29scyAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIGZpbGw9IiMwMDAwMDAiIHZlcnNpb249IjEuMSIgaWQ9IkNhcGFfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgDQoJIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDE5Ny4yNTkgMTk3LjI1OSINCgkgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQzLjU0NSwxMjcuOTEzYy0wLjQwNSwwLjA3NC0wLjczLDAuMjM5LTEuMDExLDAuNDQ2Yy0xMS4yNzEtMy4xMjctMjEuMDUyLDEuNjE3LTI3LjcwNyw5Ljc2Nw0KCQkJYy0wLjA1Ny0wLjA2NC0wLjEyMi0wLjEyMy0wLjE3OS0wLjE4OGMtMC41NjItMC42NTItMS43MTgsMC4yMjktMS4xOTgsMC45MjVjMC4xMzYsMC4xODIsMC4yODksMC4zNDQsMC40MjcsMC41MjUNCgkJCWMtMTMuNTQsMTguMTA2LTEyLjQ1Miw1MC43MjgsMTkuMzQ0LDU0LjQ4OEM3NC40OTQsMTk4Ljc1OCw4Mi45MDEsMTIwLjcyLDQzLjU0NSwxMjcuOTEzeiBNNDUuNDE4LDEzNC4wNzcNCgkJCWMwLjIxNCwwLjAyLDAuNDA1LTAuMDIzLDAuNTk2LTAuMDY5YzUuNjI1LDEuNDAzLDEwLjQzNywyLjY5OSwxNC4wMjgsOC4xMzZjMS43NjEsMi42NjYsMi42Niw1Ljc4LDMuMDAzLDguOTg4DQoJCQljLTAuODQ0LTAuOTI0LTIuMDg5LTEuNDg1LTMuMTk4LTIuMTc3Yy0yLjQ1NS0xLjUzLTQuODU1LTMuMTQ4LTcuMjI5LTQuODAxYy00LjMyOC0zLjAxMi04LjAxOC02LjQxNi0xMS44NjMtOS45Ng0KCQkJYy0wLjE1LTAuMTM5LTAuMzE5LTAuMTYyLTAuNDgzLTAuMjIyQzQxLjk1NSwxMzMuODg5LDQzLjY3MywxMzMuOTE3LDQ1LjQxOCwxMzQuMDc3eiBNMTEuNTExLDE2NC4wMDQNCgkJCWMzLjgzNSw1LjA2MSw3Ljk1Niw5LjksMTIuMzY5LDE0LjQ2MmMyLjgwNSwyLjksNS45OCw2LjE5Niw5LjQ0NSw4Ljk1NEMxOS40OTQsMTg2Ljg2MSwxMS41NzQsMTc2LjI3MSwxMS41MTEsMTY0LjAwNHoNCgkJCSBNMzkuNzI5LDE4Ni45NDFjLTQuMjg2LTMuNzgxLTkuMTkyLTcuMDM5LTEzLjMwOC0xMS4wMThjLTUuMi01LjAyNi05LjgxLTEwLjYtMTQuNjMyLTE1Ljk3Ng0KCQkJYzAuMTk0LTEuNDUzLDAuNDU5LTIuOTEyLDAuODczLTQuMzcxYzAuMTY1LTAuNTgyLDAuMzY3LTEuMTMxLDAuNTU4LTEuNjg5YzYuMTY1LDYuNjE5LDEyLjI2NSwxMy4yODEsMTguNzg0LDE5LjU2Nw0KCQkJYzMuMjM4LDMuMTIyLDguNzY2LDkuNDQ5LDE0LjE5NiwxMS4xMjZDNDQuMTY4LDE4NS42MTIsNDIuMDM5LDE4Ni40NzMsMzkuNzI5LDE4Ni45NDF6IE01MC42MDMsMTgxLjc5Mw0KCQkJYy0wLjIxMy0wLjEzMS0wLjQ0NS0wLjIzNC0wLjcxMi0wLjI3MWMtNy4wNDktMC45NDEtMTQuNDk3LTkuNzU1LTE5LjM2OS0xNC4yNjVjLTUuNTY4LTUuMTUxLTEwLjg4NS0xMC41NjMtMTYuMzUtMTUuODIzDQoJCQljMS4wOTMtMi41MTEsMi41MjYtNC42OTIsNC4xMjgtNi42ODRjNC40MzMsNC45NjUsOS4zMzQsOS40MjksMTQuNTQ5LDEzLjY4NGM3LjMwMyw1Ljk2MSwxNC45NDIsMTMuMjE2LDIzLjIxOSwxNy43Ng0KCQkJQzU0LjQ2OSwxNzguMjg4LDUyLjY1MywxODAuMTkyLDUwLjYwMywxODEuNzkzeiBNNTcuOTg2LDE3My40Yy01LjA0Mi01LjY4OC0xMi42MDMtOS43NDItMTguNjk5LTE0LjExDQoJCQljLTYuOTA2LTQuOTQ5LTEzLjY2Ni0xMC4xNjYtMTkuNzA1LTE2LjEzN2MxLjYwOC0xLjcyNCwzLjQxMS0zLjIxMiw1LjM2OC00LjQ2NWMxMS43MzMsOS40MzgsMjIuMTIzLDIyLjc4MSwzNS44NTEsMjguOTc2DQoJCQlDNjAuMDMyLDE2OS42NjUsNTkuMDk1LDE3MS41OTIsNTcuOTg2LDE3My40eiBNNjEuOTU5LDE2NC4xMmMtNi42MDktNC43MzgtMTMuNC05LjAyLTE5LjczMS0xNC4yNDQNCgkJCWMtNS4xMTItNC4yMTgtMTAuMTk4LTguMzY1LTE1LjYzNS0xMi4xMTJjMy43NDItMi4wNjIsNy45ODktMy4yNTksMTIuNDk1LTMuNjQ3Yy0wLjM4NywwLjI5Mi0wLjY0OCwwLjc3MS0wLjUyNiwxLjM1NA0KCQkJYzAuOTQ4LDQuNTQ5LDcuMzY3LDguNDY4LDEwLjcyNSwxMS4xMDNjMi40NjQsMS45MzMsNC45OTQsMy43ODEsNy41NTgsNS41ODFjMS43NjYsMS4yNCwzLjUzMiwzLjAyMSw1LjgwOCwyLjUzOQ0KCQkJYzAuMjA1LTAuMDQzLDAuMzgtMC4xNDQsMC41NDItMC4yNTljLTAuMDA0LDIuMTg3LTAuMjAxLDQuMzYzLTAuNTM1LDYuNDE3QzYyLjQ4LDE2MS45NTUsNjIuMjQ1LDE2My4wNDUsNjEuOTU5LDE2NC4xMnoiLz4NCgkJPHBhdGggZD0iTTEzMS43MzEsMTQ1Ljc5OGMwLjgwNS0wLjY3NiwxLjI1MS0xLjgyMiwwLjYxNC0yLjg2NWMtMC42NDEtMS4wNS0xLjM2Ni0yLjAyNC0yLjA0Ni0zLjAzNw0KCQkJYy05LjktMzUuNTA1LTQ1LjM3MS02Ny44NDktODAuMjgxLTc2LjIzNGMtMTQuMjgzLTMuNDMxLTMxLjQ2NC0xLjAzNC0zOC41NzYsMTMuMTg0Yy01Ljg3NiwxMS43NDcsMi44NjksMjAuNTM3LDEyLjg4MSwyNC4xNzUNCgkJCWMtMC4xNDIsMS4xNjYsMC43MDEsMi41ODcsMi4wODYsMi42ODljMTUuMTgyLDEuMTI0LDI5LjQxMSw1LjU3Nyw0MC42OSwxMy42MTJjMi43OTMsMi4zMzIsNS41NTIsNC43MDMsOC4yNTcsNy4xMzUNCgkJCWM2LjgxNSw3LjA4NiwxMS44ODcsMTYuMDAxLDE0LjQyOCwyNi45MWMzLjE4NiwxMy42NzgsNS4zODksMzAuMTA0LDE0LjE4OCw0MS41NDZjNS4yMTUsNi43OCwxMy4xMTUsNS4wNTQsMTcuOTQ2LTEuMjM1DQoJCQlDMTMyLjQ4MSwxNzcuOTI0LDEzNSwxNjIuNDU2LDEzMS43MzEsMTQ1Ljc5OHogTTU1LjMzNCw3Mi4zMjJjMTUuMzA4LDQuNTkxLDI4Ljc1NiwxNS4wNzksNDAuMTI5LDI1LjkyMg0KCQkJYzcuMDksNi43NjEsMTMuNjk5LDE0LjcyNSwxOC45NTYsMjMuMzdjLTIxLjgxNy0yMC4wNzgtNTEuMDQ3LTMzLjI5NC03My40NjItNTIuMjhDNDUuODQ1LDY5LjY3NCw1MC42OTEsNzAuOTMsNTUuMzM0LDcyLjMyMnoNCgkJCSBNMzUuMjEsNjkuMzk3YzAuNjc0LTAuMDcxLDEuMzQ4LTAuMTAxLDIuMDIzLTAuMTI3YzI1LjM3OCwyNi43NzEsNTkuOTUsNDEuOTM5LDg1LjEyMiw2OC45NzINCgkJCWMxLjU5NCw0LjU5NiwyLjczNSw5LjI4LDMuMjc4LDE0LjAwMWMtMTEuNzYxLTE1LjI3Mi0yOC43NjUtMjguMTYxLTQzLjIyNS00MC4xMzRjLTE3LjY0OC0xNC42MS0zNi45NS0yOC40NDYtNTcuNTc1LTM4LjUwMQ0KCQkJYy0wLjE0NC0wLjA3LTAuMjgyLTAuMDkyLTAuNDItMC4xMTRDMjcuMjkyLDcxLjM3NSwzMC45NzIsNjkuODQzLDM1LjIxLDY5LjM5N3ogTTM0LjA3Nyw5OS4wMTENCgkJCWMtMC4wNjQtMS4zNjctMC43NjQtMi42NDYtMi4zMTUtMi44NjFjLTEwLjQ1MS0xLjQ0Ni0xNC4zODQtNi43MTMtMTMuOTE3LTEyLjIwNWM5LjA2MSw0Ljg4NywxNy44NzMsMTAuMjY2LDI2LjM5NiwxNi4wOTENCgkJCUM0MC44MTIsOTkuNDIxLDM3LjQsOTkuMDUyLDM0LjA3Nyw5OS4wMTF6IE0xMTguMDQyLDE4NS45MzhjLTkuMzQyLDE0LjcwNS0xNi42MTQtMTUuMDYzLTE4LjIyNS0yMS4xOTINCgkJCWMtMC4xMTctMC40NDktMC4yMDMtMC45MDktMC4zMTctMS4zNThjNS44OTYsNy4yNTIsMTIuMzA1LDE0LjA2OSwxOC4yNjYsMjEuMjcxYzAuMjE2LDAuMjYxLDAuNDU1LDAuNDM0LDAuNzAxLDAuNTQyDQoJCQlDMTE4LjMxNywxODUuNDQ1LDExOC4xOTgsMTg1LjY5MywxMTguMDQyLDE4NS45Mzh6IE0xMjAuNTA2LDE4MS41NzRjLTYuNjYtNy4zNzctMTMuNjgxLTE0LjQyLTIwLjQ4Ny0yMS42Ng0KCQkJYy0wLjQ1Mi0wLjQ4LTAuOTc3LTAuNTA5LTEuMzk2LTAuMjkzYy0xLjIxOC01LjI4NC0yLjMyNC0xMC42MDktMy42OTQtMTUuODU0YzkuOTM3LDEwLjc1OSwxOS4wNDIsMjIuMjUzLDI3LjE5NywzNC4zNTkNCgkJCUMxMjEuNjM4LDE3OS4yODEsMTIxLjA4NiwxODAuNDMsMTIwLjUwNiwxODEuNTc0eiBNMTIzLjU5NCwxNzQuMjNjLTkuMDc0LTE0LjMxNy0xOS44MTItMjcuNzg1LTMxLjgyMS00MC4xMg0KCQkJYy02LjA2Ni0xNC4zNzctMjAuNDM5LTI1LjQ1LTM2LjE3Ni0zMS4wNDZjLTExLjgxNS04LjQ0NC0yNC4yNDUtMTUuODg1LTM3LjA1LTIyLjE0NmMwLjgyMy0yLjIwMywyLjMyNS00LjMyMiw0LjMzNS02LjE0OA0KCQkJYy0wLjAxOCwwLjQwOCwwLjE0NiwwLjgyMywwLjYyOCwxLjEwM2MxOS44MTIsMTEuNTE2LDM4LjAwNywyNC44NTksNTUuNjQ0LDM5LjQ5MWMxNi40NzIsMTMuNjY2LDMwLjY5OSwyOS44MTQsNDYuODgxLDQzLjYxMg0KCQkJQzEyNiwxNjQuMDgsMTI1LjI0LDE2OS4xOSwxMjMuNTk0LDE3NC4yM3oiLz4NCgkJPHBhdGggZD0iTTE4OS4zNzMsMTM0LjE0M2MtOC40NDUtNTcuMDgzLTQzLjI5Mi0xMDMuNzEtOTcuNjIyLTEyMy42MjhDNzMuMjU1LDMuNzM0LDQxLjY2Ny04LjcwMywyNi4wMjMsOS4zNzYNCgkJCUMxOC40NTksMTguMTE4LDE0LjkzMiwzOSwyOC40MjgsNDEuODY0YzAuMTczLDAuMzY3LDAuNDY5LDAuNjg0LDAuOTIxLDAuODRjMTguOTgyLDYuNTcxLDM5LjYwMyw2LjE4Niw1OC44MzMsMTIuMzk4DQoJCQljMjMuNDM2LDcuNTcsNDAuNzY0LDMwLjAwOSw0OS45MjksNTEuNzk0YzkuNDUyLDIyLjQ2Nyw0LjEzNCw0Ni40MzksOS43ODIsNjkuNjE0YzMuNzE1LDE1LjIzOCwxNS44NTIsMjAuNTA2LDI5LjcyNSwxNC4wODUNCgkJCUMxOTYuMjg2LDE4MS45NTYsMTkxLjY1NywxNDkuNTgzLDE4OS4zNzMsMTM0LjE0M3ogTTE0OC43MDUsNTcuMjE3YzAuMzY4LDAuNDM1LDAuNjkyLDAuOTE0LDEuMDU1LDEuMzU2DQoJCQljLTkuNzE1LTkuMTIxLTIwLjU2Mi0xNy4zOTgtMzAuMTEtMjYuNjQ5QzEzMC4zNTcsMzguNzUyLDE0MC4wOTQsNDcuMDMsMTQ4LjcwNSw1Ny4yMTd6IE0xMDkuNDcyLDI2LjAxDQoJCQljNy4yMTcsOS4zNzksMTUuNjUsMTcuNTcyLDI0LjI4NiwyNS42NDVjOS43NSw5LjExNiwxOC44MTIsMTkuMDAxLDI4LjU4MywyOC4wNDRjMC40OSwwLjQ1MywxLjA3OCwwLjYwOSwxLjY1MiwwLjU3Ng0KCQkJYzEuMzI1LDIuNTI1LDIuNTk5LDUuMDg1LDMuNzkxLDcuNjgyYy0yNy40MjItMjEuMjI4LTUxLjQ1NS00NS42MDUtNzYuMDk3LTY5LjkxNkM5Ny44NjYsMjAuMzYxLDEwMy43ODEsMjMuMDI3LDEwOS40NzIsMjYuMDF6DQoJCQkgTTg1LjY5OSwxNS44NTFjMC41NzUsMC4xOTMsMS4xMjMsMC40MjIsMS42OTQsMC42MmMyNS4wNjIsMjkuNTU1LDUyLjkzMyw1Ny4zMjIsODQuMTUzLDgwLjMyNQ0KCQkJYzEuMDcxLDIuNzI3LDIuMDk4LDUuNDY1LDMuMDMyLDguMjE4Yy0xMy42MzItMTkuMzk5LTM3LjkwMS0zMy4yODItNTUuNTI4LTQ3LjkyM0MxMDAuNDEyLDQxLjYxLDgwLjkwNiwyMC4xMTYsNTguOTE2LDguMjk0DQoJCQlDNjguMDY1LDkuNzk5LDc3LjIzMSwxMy4wMTMsODUuNjk5LDE1Ljg1MXogTTM1LjAxOCwzOS44MThjMC40NTItMS44NTItMC41MjctNC4wOTctMy4wMTgtNC4xMjkNCgkJCWMtNy42NzgtMC4wOTgtNi42NDctNy43NDMtNC43NjctMTMuNDg5YzguOTI1LDYuNjUzLDE4LjIwOSwxMi43OTksMjcuMjgyLDE5LjI1MUM0OC4wOTIsNDAuNjI3LDQxLjU4LDQwLjA3NywzNS4wMTgsMzkuODE4eg0KCQkJIE02Mi42NjMsNDIuNjg1QzUyLjQ5NywzMy40NjUsMzkuNTk2LDI3LjQxNywyOC4wOCwyMC4xMmMtMC4wMzItMC4wMi0wLjA2LTAuMDE5LTAuMDkyLTAuMDM2DQoJCQljMC41NzctMS40ODksMS4xMzktMi43MjQsMS40ODktMy40MzljMS4yMDMtMi40NTMsMi45MDUtNC4yMzksNC44ODMtNS41OTNjMTcuMzkyLDEzLjAyOSwzNS4zNzgsMjUuMjUzLDUyLjkxLDM4LjA3Ng0KCQkJQzc5LjQyOSw0Ni4yNjksNzEuMTY4LDQ0LjE2OSw2Mi42NjMsNDIuNjg1eiBNMTMxLjU2MSw4MS4yNzZjLTguNjM1LTExLjgwNi0xOS41NTQtMjAuNTA4LTMxLjk0Ni0yNi43OTkNCgkJCUM3OS41NjQsMzguODgyLDU4LjQ5MiwyNC4yODIsMzcuMDY4LDkuNTI4YzMuNDI5LTEuNTA1LDcuMzk3LTIuMDQxLDExLjMwNS0yLjEwNmMxLjc5Ni0wLjAzLDMuNjA0LDAuMDY0LDUuNDE3LDAuMjINCgkJCWMzOC4wMjIsMzguMjksODUuNzMyLDY2LjQ5MiwxMjIuMzU1LDEwNi4zMzVjMC40MTcsMC40NTQsMC45MDksMC41NjYsMS4zNzIsMC40OTVjMC4yODQsMS4wMDYsMC42MDQsMi4wMTUsMC44NywzLjAyDQoJCQljMS43NjUsNi42NjEsMy41NTgsMTQuMTU5LDQuNzI2LDIxLjg1NkMxNjcuODUyLDExNy40NjYsMTUwLjQzNSw5OC41MDgsMTMxLjU2MSw4MS4yNzZ6IE0xNTQuNjQsMTc0LjY0OA0KCQkJYy0xLjQ1Ny00LjIzNC0yLjE4OS04LjcyOC0yLjYxMi0xMy4yNjVjMS44OTYsMi4yMjUsMy44MzEsNC40MSw1Ljc0NCw2LjYxN2M1LjEsNS44ODYsMTAuNTM2LDExLjU1OSwxNi4wNjEsMTcuMDQ1DQoJCQlDMTY2LjYwNiwxODkuMzU5LDE1Ny41ODEsMTgzLjE4OSwxNTQuNjQsMTc0LjY0OHogTTE3OS4xNTMsMTc5LjExOGMtMC42MTgsMS4xNTctMS4yOTcsMi4xMTEtMS45OTksMi45NzcNCgkJCWMtNS41NjYtNS41ODktMTEuNDA5LTEwLjkwNS0xNy4wMjItMTYuNDVjLTIuNTctMi41MzktNS4yNjEtNS41Ny04LjM2My03LjU4OWMtMC4yLTMuMTI0LTAuMzAxLTYuMjQ0LTAuNDI0LTkuMjkNCgkJCWMtMC4wMDktMC4yMjItMC4wMjEtMC40My0wLjAzMS0wLjY0OWM0LjYxMSw0LjQwOCw5LjE0Niw4Ljg1NCwxMy40MjQsMTMuNjEyYzUuMDMzLDUuNTk0LDkuMzE3LDExLjgzOCwxNC40MjYsMTcuMzcNCgkJCUMxNzkuMTYxLDE3OS4xMDQsMTc5LjE1OCwxNzkuMTExLDE3OS4xNTMsMTc5LjExOHogTTE4MS4xNiwxNzQuNjE1Yy04LjA1MS0xMC45MzItMTguOTczLTIxLjIxMi0yOS45NzEtMjkuMDk2DQoJCQljLTAuMjYzLTUuNTMzLTAuNjQ2LTEwLjc1Ny0xLjI3OC0xNS44NzhjNS41NTYsNS45NDcsMTAuOTM4LDEyLjA0NiwxNi4zMDcsMTguMTY3YzQuODIyLDUuNDk3LDEwLjU1NCwxNC4yNTksMTcuMzU3LDE3LjU4Mw0KCQkJQzE4My4wNTEsMTY4LjU3NCwxODIuMjgyLDE3MS42NzIsMTgxLjE2LDE3NC42MTV6IE0xODQuMjMyLDE1OS42NTJjLTMuODQyLTQuNjczLTkuNDUzLTguNzg2LTEzLjQ4MS0xMi43NzUNCgkJCWMtNy4xNTItNy4wODItMTQuMTkxLTE0LjI4LTIxLjQxMy0yMS4yOTFjLTEuMjk1LTguMzY1LTMuNDA0LTE2LjU2My03LjEyNy0yNS40MTNjLTAuNDgyLTEuMTQ0LTEuMDIzLTIuMjEyLTEuNTM2LTMuMzE2DQoJCQljMTMuNjMsMTYuNDA1LDI2LjExNiwzMy45MDksNDIuNDc0LDQ3LjAyYzAuMTg0LDAuMTQ3LDAuMzkzLDAuMjE5LDAuNjA1LDAuMjU5QzE4NC4zMzQsMTQ5LjMzNywxODQuNTc3LDE1NC41NzIsMTg0LjIzMiwxNTkuNjUyeg0KCQkJIi8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+";
        //create a div with the icon
        const div = document.createElement('div');
        div.appendChild(document.createElement('img'));
        div.querySelector('img').src = rssIcon;
        div.querySelector('img').alt = 'rss';


        const icons = {
            "twitter": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBVcGxvYWRlZCB0bzogU1ZHIFJlcG8sIHd3dy5zdmdyZXBvLmNvbSwgR2VuZXJhdG9yOiBTVkcgUmVwbyBNaXhlciBUb29scyAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkNhcGFfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgDQoJIHZpZXdCb3g9IjAgMCA0NTUuNzMxIDQ1NS43MzEiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPHJlY3QgeD0iMCIgeT0iMCIgc3R5bGU9ImZpbGw6IzUwQUJGMTsiIHdpZHRoPSI0NTUuNzMxIiBoZWlnaHQ9IjQ1NS43MzEiLz4NCgk8cGF0aCBzdHlsZT0iZmlsbDojRkZGRkZGOyIgZD0iTTYwLjM3NywzMzcuODIyYzMwLjMzLDE5LjIzNiw2Ni4zMDgsMzAuMzY4LDEwNC44NzUsMzAuMzY4YzEwOC4zNDksMCwxOTYuMTgtODcuODQxLDE5Ni4xOC0xOTYuMTgNCgkJYzAtMi43MDUtMC4wNTctNS4zOS0wLjE2MS04LjA2N2MzLjkxOS0zLjA4NCwyOC4xNTctMjIuNTExLDM0LjA5OC0zNWMwLDAtMTkuNjgzLDguMTgtMzguOTQ3LDEwLjEwNw0KCQljLTAuMDM4LDAtMC4wODUsMC4wMDktMC4xMjMsMC4wMDljMCwwLDAuMDM4LTAuMDE5LDAuMTA0LTAuMDY2YzEuNzc1LTEuMTg2LDI2LjU5MS0xOC4wNzksMjkuOTUxLTM4LjIwNw0KCQljMCwwLTEzLjkyMiw3LjQzMS0zMy40MTUsMTMuOTMyYy0zLjIyNywxLjA3Mi02LjYwNSwyLjEyNi0xMC4wODgsMy4xMDNjLTEyLjU2NS0xMy40MS0zMC40MjUtMjEuNzgtNTAuMjUtMjEuNzgNCgkJYy0zOC4wMjcsMC02OC44NDEsMzAuODA1LTY4Ljg0MSw2OC44MDNjMCw1LjM2MiwwLjYxNywxMC41ODEsMS43ODQsMTUuNTkyYy01LjMxNC0wLjIxOC04Ni4yMzctNC43NTUtMTQxLjI4OS03MS40MjMNCgkJYzAsMC0zMi45MDIsNDQuOTE3LDE5LjYwNyw5MS4xMDVjMCwwLTE1Ljk2Mi0wLjYzNi0yOS43MzMtOC44NjRjMCwwLTUuMDU4LDU0LjQxNiw1NC40MDcsNjguMzI5YzAsMC0xMS43MDEsNC40MzItMzAuMzY4LDEuMjcyDQoJCWMwLDAsMTAuNDM5LDQzLjk2OCw2My4yNzEsNDguMDc3YzAsMC00MS43NzcsMzcuNzQtMTAxLjA4MSwyOC44ODVMNjAuMzc3LDMzNy44MjJ6Ii8+DQo8L2c+DQo8L3N2Zz4=",
            "facebook": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBVcGxvYWRlZCB0bzogU1ZHIFJlcG8sIHd3dy5zdmdyZXBvLmNvbSwgR2VuZXJhdG9yOiBTVkcgUmVwbyBNaXhlciBUb29scyAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIGhlaWdodD0iODAwcHgiIHdpZHRoPSI4MDBweCIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiANCgkgdmlld0JveD0iMCAwIDQ1NS43MyA0NTUuNzMiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggc3R5bGU9ImZpbGw6IzNBNTU5RjsiIGQ9Ik0wLDB2NDU1LjczaDI0Mi43MDRWMjc5LjY5MWgtNTkuMzN2LTcxLjg2NGg1OS4zM3YtNjAuMzUzYzAtNDMuODkzLDM1LjU4Mi03OS40NzUsNzkuNDc1LTc5LjQ3NQ0KCWg2Mi4wMjV2NjQuNjIyaC00NC4zODJjLTEzLjk0NywwLTI1LjI1NCwxMS4zMDctMjUuMjU0LDI1LjI1NHY0OS45NTNoNjguNTIxbC05LjQ3LDcxLjg2NGgtNTkuMDUxVjQ1NS43M0g0NTUuNzNWMEgweiIvPg0KPC9zdmc+",
            "github": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+DQo8IS0tIFVwbG9hZGVkIHRvOiBTVkcgUmVwbywgd3d3LnN2Z3JlcG8uY29tLCBHZW5lcmF0b3I6IFNWRyBSZXBvIE1peGVyIFRvb2xzIC0tPgo8c3ZnIA0KICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIg0KICAgeG1sbnM6Y2M9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zIyINCiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyINCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciDQogICB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciDQogICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiDQogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSINCiAgIGhlaWdodD0iNDAwIg0KICAgd2lkdGg9IjQwMCINCiAgIGlkPSJzdmcyIg0KICAgdmVyc2lvbj0iMS4xIg0KICAgaW5rc2NhcGU6dmVyc2lvbj0iMC45MSByMTM3MjUiDQogICBzb2RpcG9kaTpkb2NuYW1lPSJnaXRodWIuc3ZnIj4NCiAgPG1ldGFkYXRhDQogICAgIGlkPSJtZXRhZGF0YTEwIj4NCiAgICA8cmRmOlJERj4NCiAgICAgIDxjYzpXb3JrDQogICAgICAgICByZGY6YWJvdXQ9IiI+DQogICAgICAgIDxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0Pg0KICAgICAgICA8ZGM6dHlwZQ0KICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPg0KICAgICAgICA8ZGM6dGl0bGUgLz4NCiAgICAgIDwvY2M6V29yaz4NCiAgICA8L3JkZjpSREY+DQogIDwvbWV0YWRhdGE+DQogIDxkZWZzDQogICAgIGlkPSJkZWZzOCIgLz4NCiAgPHNvZGlwb2RpOm5hbWVkdmlldw0KICAgICBwYWdlY29sb3I9IiNmZmZmZmYiDQogICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2Ig0KICAgICBib3JkZXJvcGFjaXR5PSIxIg0KICAgICBvYmplY3R0b2xlcmFuY2U9IjEwIg0KICAgICBncmlkdG9sZXJhbmNlPSIxMCINCiAgICAgZ3VpZGV0b2xlcmFuY2U9IjEwIg0KICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMCINCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiINCiAgICAgaW5rc2NhcGU6d2luZG93LXdpZHRoPSIxOTIwIg0KICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSIxMDU2Ig0KICAgICBpZD0ibmFtZWR2aWV3NiINCiAgICAgc2hvd2dyaWQ9ImZhbHNlIg0KICAgICBpbmtzY2FwZTp6b29tPSIwLjk0NCINCiAgICAgaW5rc2NhcGU6Y3g9IjI1MC4xNDM5NiINCiAgICAgaW5rc2NhcGU6Y3k9IjI5OS44NjI1Ig0KICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMTkyMCINCiAgICAgaW5rc2NhcGU6d2luZG93LXk9IjI0Ig0KICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIxIg0KICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJzdmcyIiAvPg0KICA8cGF0aA0KICAgICBkPSJtIDAsMjE5LjA2MjUgcSAwLDUwIDE1LDc5Ljc5MTY2IDE2LjA0MTY2NywzMS42NjY2NyA0OS43OTE2NjcsNDYuODc1IDM0LjE2NjY2NiwxNi4wNDE2NyA2OS43OTE2NjMsMTkuNzkxNjcgMzIuMDgzMzQsMy43NSA2OS4xNjY2NywzLjc1IDE2LjA0MTY3LDAgNDAuMjA4MzMsLTEuMjUgMjAuMjA4MzQsLTEuMjUgNDMuMDIwODQsLTUuNTIwODMgMjIuODEyNSwtNC4yNzA4NCAzOS4yNzA4MywtMTEuMTQ1ODQgUSAzNDUsMzQzLjQzNzUgMzYwLjgzMzMzLDMzMCAzNzYuNjY2NjcsMzE2LjU2MjUgMzg1LjQxNjY3LDI5OC44NTQxNiA0MDAsMjY5LjA2MjUgNDAwLDIxOS4wNjI1IHEgMCwtNTcuMDgzMzQgLTMyLjcwODMzLC05NS40MTY2NyA2LjQ1ODMzLC0xOS4zNzUgNi40NTgzMywtNDAuNjI0OTk1IDAsLTI4LjEyNSAtMTIuMDgzMzMsLTUyLjI5MTY3IC0yNC43OTE2NywwIC00NC4yNzA4NCw4Ljc1IC0xOS40NzkxNiw4Ljc1IC00Ni4xNDU4MywyOS41ODMzNCAtMzIuNzA4MzMsLTcuNzA4MzQgLTY3LjUsLTcuNzA4MzQgLTM3LjkxNjY3LDAgLTc0LjE2NjY3LDguMzMzMzQgLTI3LjI5MTY2LC0yMS40NTgzNCAtNDYuNzcwODMsLTMwLjIwODM0IC0xOS40NzkxNjcsLTguNzUgLTQ0LjI3MDgzMywtOC43NSBRIDI2LjI1LDU0LjY4NzUwNSAyNi4yNSw4My4wMjA4MzUgcSAwLDIxLjQ1ODMyNSA2LjQ1ODMzMyw0MS4wNDE2NzUgUSAwLDE2Mi4zOTU4MyAwLDIxOS4wNjI1IFogbSA1My43NSw0Mi4yOTE2NiBxIDAsLTI5LjU4MzMzIDE2Ljk3OTE2NywtNDkuMzc1IDE2Ljk3OTE2NiwtMTkuNzkxNjYgNDQuNjg3NTAzLC0xOS43OTE2NiAxMS4wNDE2NiwwIDQ2Ljg3NSw1LjIwODMzIDE4LjMzMzMzLDIuNzA4MzMgMzcuNzA4MzMsMi43MDgzMyAxOS4zNzUsMCAzNy45MTY2NywtMi43MDgzMyAzNi40NTgzMywtNS4yMDgzMyA0Ni44NzUsLTUuMjA4MzMgMjcuNzA4MzMsMCA0NC41ODMzMywxOS43OTE2NiAxNi44NzUsMTkuNzkxNjcgMTYuODc1LDQ5LjM3NSAwLDIxLjA0MTY4IC03LjcwODMzLDM3LjA4MzM0IC03LjcwODM0LDE1LjQxNjY2IC0xOS4yNzA4NCwyNC40NzkxNiAtMTEuNTYyNSw5LjA2MjUxIC0yOS40NzkxNiwxNC40NzkxNyAtMjkuNTgzMzQsOC45NTgzMyAtNjkuNTgzMzQsOC45NTgzMyBsIC00MC40MTY2NiwwIHEgLTE5Ljc5MTY3LDAgLTM1LjgzMzM0LC0xLjg3NSBRIDEyNi44NzUsMzQyLjgxMjUgMTA5LjU4MzMzLDMzNy4xODc1IDkyLjI5MTY2NywzMzEuNTYyNSA4MS4wNDE2NjcsMzIzLjAyMDg0IDY4Ljc1LDMxMy40Mzc1IDYxLjI1LDI5Ny41IHEgLTcuNSwtMTUuOTM3NSAtNy41LC0zNi4xNDU4NCB6IG0gMzguNTQxNjY3LDAgcSAwLDkuNzkxNjggMy4xMjUsMTkuNzkxNjggMy4xMjUsMTAuODMzMzIgMTAuNzI5MTYzLDE4LjY0NTgyIDcuNjA0MTcsNy44MTI1IDE2Ljk3OTE3LDcuODEyNSAxMCwwIDE3LjUsLTguMTI1IDEzLjEyNSwtMTUuMjA4MzIgMTMuMTI1LC0zOC4xMjUgMCwtOS41ODMzMyAtMi43MDgzMywtMTkuNTgzMzMgLTMuMTI1LC0xMC44MzMzMiAtMTAuNzI5MTcsLTE4LjY0NTgyIC03LjYwNDE3LC03LjgxMjUgLTE3LjE4NzUsLTcuODEyNSAtMTAsMCAtMTcuNSw4LjMzMzMyIC0xMy4zMzMzMzMsMTQuMzc1IC0xMy4zMzMzMzMsMzcuNzA4MzMgeiBtIDE1My45NTgzMzMsMCBxIDAsOS43OTE2OCAzLjEyNSwxOS43OTE2OCAzLjEyNSwxMC44MzMzMiAxMC42MjUsMTguNjQ1ODIgNy41LDcuODEyNSAxNi44NzUsNy44MTI1IDEwLjIwODMzLDAgMTcuNzA4MzMsLTguMTI1IDEzLjEyNSwtMTUuMjA4MzIgMTMuMTI1LC0zOC4xMjUgMCwtOC45NTgzMyAtMi45MTY2NiwtMTkuNTgzMzMgLTMuMTI1LC0xMC44MzMzMiAtMTAuNzI5MTcsLTE4LjY0NTgyIC03LjYwNDE3LC03LjgxMjUgLTE3LjE4NzUsLTcuODEyNSAtMTAsMCAtMTcuMDgzMzMsOC4zMzMzMiAtMTMuNTQxNjcsMTQuMzc1IC0xMy41NDE2NywzNy43MDgzMyB6Ig0KICAgICBpZD0iZ2l0aHViIg0KICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIj4NCiAgICA8dGl0bGUNCiAgICAgICBpZD0idGl0bGUyMzI4MiI+Z2l0aHViPC90aXRsZT4NCiAgPC9wYXRoPg0KPC9zdmc+",
            "email": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IS0tIFVwbG9hZGVkIHRvOiBTVkcgUmVwbywgd3d3LnN2Z3JlcG8uY29tLCBHZW5lcmF0b3I6IFNWRyBSZXBvIE1peGVyIFRvb2xzIC0tPg0KPHN2ZyBmaWxsPSIjMDAwMDAwIiB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCAxOTIwIDE5MjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQogICAgPHBhdGggZD0iTTAgMTY5NC4yMzVoMTkyMFYyMjZIMHYxNDY4LjIzNVpNMTEyLjk0MSAzNzYuNjY0VjMzOC45NEgxODA3LjA2djM3LjcyM0w5NjAgMTExMS4yMzNsLTg0Ny4wNTktNzM0LjU3Wk0xODA3LjA2IDUyNi4xOTh2OTUwLjUxM2wtMzUxLjEzNC00MzguODktODguMzIgNzAuNDc1IDM3OC4zNTMgNDcyLjk5OEgxNzQuMDQybDM3OC4zNTMtNDcyLjk5OC04OC4zMi03MC40NzUtMzUxLjEzNCA0MzguODlWNTI2LjE5OEw5NjAgMTI2MC43NjhsODQ3LjA1OS03MzQuNTdaIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz4NCjwvc3ZnPg==",
            "phone": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IS0tIFVwbG9hZGVkIHRvOiBTVkcgUmVwbywgd3d3LnN2Z3JlcG8uY29tLCBHZW5lcmF0b3I6IFNWRyBSZXBvIE1peGVyIFRvb2xzIC0tPg0KPHN2ZyB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjxwYXRoIGQ9Ik00Ljg1OTA0IDZDNi42NzM5NiA0LjE0ODY0IDkuMjAzMDggMyAxMi4wMDA1IDNDMTQuNzk3OSAzIDE3LjMyNyA0LjE0ODY0IDE5LjE0MTkgNk0xNi40NzI3IDlDMTUuMzc0IDcuNzcyNSAxMy43Nzc0IDcgMTIuMDAwNCA3QzEwLjIyMzQgNyA4LjYyNjg3IDcuNzcyNSA3LjUyODIzIDlNMy4zOTE5OSAxNi41NzFDOC4xNzE2MSAxMS44MzUxIDE1Ljg4NTUgMTEuODcwNCAyMC42MjE1IDE2LjY1MDFDMjAuNzY1OSAxNi43OTU4IDIwLjkwNTkgMTYuOTQ0MiAyMS4wNDE0IDE3LjA5NTJDMjEuMzI0MyAxNy40MTA1IDIxLjQ2NTggMTcuNTY4MSAyMS41Mjg4IDE3Ljc5MTdDMjEuNTgwMiAxNy45NzM4IDIxLjU3MzMgMTguMjExOCAyMS41MTEzIDE4LjM5MDZDMjEuNDM1MiAxOC42MTAxIDIxLjI2NTMgMTguNzc4NCAyMC45MjU1IDE5LjExNTFMMTkuNzI5OCAyMC4yOTk5QzE5LjQ0MyAyMC41ODQxIDE5LjI5OTYgMjAuNzI2MiAxOS4xMjk5IDIwLjgwMDhDMTguOTggMjAuODY2NyAxOC44MTYyIDIwLjg5NDggMTguNjUyOSAyMC44ODI2QzE4LjQ2OCAyMC44Njg4IDE4LjI4NTUgMjAuNzgyNiAxNy45MjA0IDIwLjYxMDJMMTUuOTY3MyAxOS42ODc4QzE1LjU0MjUgMTkuNDg3MiAxNS4zMzAxIDE5LjM4NjkgMTUuMTkyNCAxOS4yMjg1QzE1LjA3MDkgMTkuMDg4OSAxNC45OTA2IDE4LjkxODMgMTQuOTYwNCAxOC43MzU3QzE0LjkyNjEgMTguNTI4NiAxNC45ODQxIDE4LjMwMSAxNS4xMDAxIDE3Ljg0NThMMTUuMzQwMiAxNi45MDM3QzEzLjIwMzcgMTYuMDg5NyAxMC44MTQyIDE2LjA3NzIgOC42NzA3MyAxNi44NzMyTDguOTAyMiAxNy44MTc0QzkuMDE0MDQgMTguMjczNyA5LjA2OTk3IDE4LjUwMTggOS4wMzM3NyAxOC43MDg1QzkuMDAxODQgMTguODkwOCA4LjkxOTk3IDE5LjA2MDcgOC43OTcyNSAxOS4xOTkyQzguNjU4MDcgMTkuMzU2MyA4LjQ0NDc3IDE5LjQ1NDYgOC4wMTgxNyAxOS42NTEzTDYuMDU2NjggMjAuNTU1OEM1LjY5MDAzIDIwLjcyNDggNS41MDY2OSAyMC44MDk0IDUuMzIxNzEgMjAuODIxNUM1LjE1ODMgMjAuODMyMiA0Ljk5NDc3IDIwLjgwMjYgNC44NDU0OCAyMC43MzUzQzQuNjc2NDYgMjAuNjU5MiA0LjUzNDM3IDIwLjUxNTggNC4yNTAxOCAyMC4yMjg5TDMuMDY1MzcgMTkuMDMzMkMyLjcyODY2IDE4LjY5MzQgMi41NjAzMSAxOC41MjM1IDIuNDg2MjggMTguMzAzNEMyLjQyNTk2IDE4LjEyNCAyLjQyMTE3IDE3Ljg4NiAyLjQ3NDIyIDE3LjcwNDNDMi41MzkzNCAxNy40ODEzIDIuNjgyMjQgMTcuMzI1IDIuOTY4MDQgMTcuMDEyNEMzLjEwNDk1IDE2Ljg2MjYgMy4yNDYyNyAxNi43MTU0IDMuMzkxOTkgMTYuNTcxWiIgc3Ryb2tlPSIjMDAwMDAwIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPg0KPC9zdmc+"
        };

        const renderedLinks = links.map(link => {
            const icon = icons[link.name];
            if (!icon) {
                console.warn(`Social media not found "${link.name}"`);
                console.warn(`Available social media: ${Object.keys(icons).join(', ')}`)
                return;
            }
            const hasTargetBlank = link.name === 'phone';
            const target = hasTargetBlank ? ``:`target="blank"`

            return `
                <p>
                    <a href="${link.url}" ${target}>
                        <img src="${icon}" alt="${link.name}">
                    </a>
                </p>
            `;
        });

        this.shadowRoot.innerHTML = `
            <style>
                .social-links-container {
    display: flex;
flex-direction: row-reverse;
justify-content: center;
                }
                a {
                    color: var(--base);
                    text-decoration: none;
                }
                a:hover {
                    text-decoration: underline;
                }
                img:hover {
                    opacity: 0.8;
                }
                img {
                    width: 20px;
                    height: 20px;
                    vertical-align: middle;
                    margin-right: 5px;
                }
                .rss-icon {
                    background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBVcGxvYWRlZCB0bzogU1ZHIFJlcG8sIHd3dy5zdmdyZXBvLmNvbSwgR2VuZXJhdG9yOiBTVkcgUmVwbyBNaXhlciBUb29scyAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIGZpbGw9IiMwMDAwMDAiIHZlcnNpb249IjEuMSIgaWQ9IkNhcGFfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgDQoJIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDE5Ny4yNTkgMTk3LjI1OSINCgkgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8Zz4NCgk8Zz4NCgkJPHBhdGggZD0iTTQzLjU0NSwxMjcuOTEzYy0wLjQwNSwwLjA3NC0wLjczLDAuMjM5LTEuMDExLDAuNDQ2Yy0xMS4yNzEtMy4xMjctMjEuMDUyLDEuNjE3LTI3LjcwNyw5Ljc2Nw0KCQkJYy0wLjA1Ny0wLjA2NC0wLjEyMi0wLjEyMy0wLjE3OS0wLjE4OGMtMC41NjItMC42NTItMS43MTgsMC4yMjktMS4xOTgsMC45MjVjMC4xMzYsMC4xODIsMC4yODksMC4zNDQsMC40MjcsMC41MjUNCgkJCWMtMTMuNTQsMTguMTA2LTEyLjQ1Miw1MC43MjgsMTkuMzQ0LDU0LjQ4OEM3NC40OTQsMTk4Ljc1OCw4Mi45MDEsMTIwLjcyLDQzLjU0NSwxMjcuOTEzeiBNNDUuNDE4LDEzNC4wNzcNCgkJCWMwLjIxNCwwLjAyLDAuNDA1LTAuMDIzLDAuNTk2LTAuMDY5YzUuNjI1LDEuNDAzLDEwLjQzNywyLjY5OSwxNC4wMjgsOC4xMzZjMS43NjEsMi42NjYsMi42Niw1Ljc4LDMuMDAzLDguOTg4DQoJCQljLTAuODQ0LTAuOTI0LTIuMDg5LTEuNDg1LTMuMTk4LTIuMTc3Yy0yLjQ1NS0xLjUzLTQuODU1LTMuMTQ4LTcuMjI5LTQuODAxYy00LjMyOC0zLjAxMi04LjAxOC02LjQxNi0xMS44NjMtOS45Ng0KCQkJYy0wLjE1LTAuMTM5LTAuMzE5LTAuMTYyLTAuNDgzLTAuMjIyQzQxLjk1NSwxMzMuODg5LDQzLjY3MywxMzMuOTE3LDQ1LjQxOCwxMzQuMDc3eiBNMTEuNTExLDE2NC4wMDQNCgkJCWMzLjgzNSw1LjA2MSw3Ljk1Niw5LjksMTIuMzY5LDE0LjQ2MmMyLjgwNSwyLjksNS45OCw2LjE5Niw5LjQ0NSw4Ljk1NEMxOS40OTQsMTg2Ljg2MSwxMS41NzQsMTc2LjI3MSwxMS41MTEsMTY0LjAwNHoNCgkJCSBNMzkuNzI5LDE4Ni45NDFjLTQuMjg2LTMuNzgxLTkuMTkyLTcuMDM5LTEzLjMwOC0xMS4wMThjLTUuMi01LjAyNi05LjgxLTEwLjYtMTQuNjMyLTE1Ljk3Ng0KCQkJYzAuMTk0LTEuNDUzLDAuNDU5LTIuOTEyLDAuODczLTQuMzcxYzAuMTY1LTAuNTgyLDAuMzY3LTEuMTMxLDAuNTU4LTEuNjg5YzYuMTY1LDYuNjE5LDEyLjI2NSwxMy4yODEsMTguNzg0LDE5LjU2Nw0KCQkJYzMuMjM4LDMuMTIyLDguNzY2LDkuNDQ5LDE0LjE5NiwxMS4xMjZDNDQuMTY4LDE4NS42MTIsNDIuMDM5LDE4Ni40NzMsMzkuNzI5LDE4Ni45NDF6IE01MC42MDMsMTgxLjc5Mw0KCQkJYy0wLjIxMy0wLjEzMS0wLjQ0NS0wLjIzNC0wLjcxMi0wLjI3MWMtNy4wNDktMC45NDEtMTQuNDk3LTkuNzU1LTE5LjM2OS0xNC4yNjVjLTUuNTY4LTUuMTUxLTEwLjg4NS0xMC41NjMtMTYuMzUtMTUuODIzDQoJCQljMS4wOTMtMi41MTEsMi41MjYtNC42OTIsNC4xMjgtNi42ODRjNC40MzMsNC45NjUsOS4zMzQsOS40MjksMTQuNTQ5LDEzLjY4NGM3LjMwMyw1Ljk2MSwxNC45NDIsMTMuMjE2LDIzLjIxOSwxNy43Ng0KCQkJQzU0LjQ2OSwxNzguMjg4LDUyLjY1MywxODAuMTkyLDUwLjYwMywxODEuNzkzeiBNNTcuOTg2LDE3My40Yy01LjA0Mi01LjY4OC0xMi42MDMtOS43NDItMTguNjk5LTE0LjExDQoJCQljLTYuOTA2LTQuOTQ5LTEzLjY2Ni0xMC4xNjYtMTkuNzA1LTE2LjEzN2MxLjYwOC0xLjcyNCwzLjQxMS0zLjIxMiw1LjM2OC00LjQ2NWMxMS43MzMsOS40MzgsMjIuMTIzLDIyLjc4MSwzNS44NTEsMjguOTc2DQoJCQlDNjAuMDMyLDE2OS42NjUsNTkuMDk1LDE3MS41OTIsNTcuOTg2LDE3My40eiBNNjEuOTU5LDE2NC4xMmMtNi42MDktNC43MzgtMTMuNC05LjAyLTE5LjczMS0xNC4yNDQNCgkJCWMtNS4xMTItNC4yMTgtMTAuMTk4LTguMzY1LTE1LjYzNS0xMi4xMTJjMy43NDItMi4wNjIsNy45ODktMy4yNTksMTIuNDk1LTMuNjQ3Yy0wLjM4NywwLjI5Mi0wLjY0OCwwLjc3MS0wLjUyNiwxLjM1NA0KCQkJYzAuOTQ4LDQuNTQ5LDcuMzY3LDguNDY4LDEwLjcyNSwxMS4xMDNjMi40NjQsMS45MzMsNC45OTQsMy43ODEsNy41NTgsNS41ODFjMS43NjYsMS4yNCwzLjUzMiwzLjAyMSw1LjgwOCwyLjUzOQ0KCQkJYzAuMjA1LTAuMDQzLDAuMzgtMC4xNDQsMC41NDItMC4yNTljLTAuMDA0LDIuMTg3LTAuMjAxLDQuMzYzLTAuNTM1LDYuNDE3QzYyLjQ4LDE2MS45NTUsNjIuMjQ1LDE2My4wNDUsNjEuOTU5LDE2NC4xMnoiLz4NCgkJPHBhdGggZD0iTTEzMS43MzEsMTQ1Ljc5OGMwLjgwNS0wLjY3NiwxLjI1MS0xLjgyMiwwLjYxNC0yLjg2NWMtMC42NDEtMS4wNS0xLjM2Ni0yLjAyNC0yLjA0Ni0zLjAzNw0KCQkJYy05LjktMzUuNTA1LTQ1LjM3MS02Ny44NDktODAuMjgxLTc2LjIzNGMtMTQuMjgzLTMuNDMxLTMxLjQ2NC0xLjAzNC0zOC41NzYsMTMuMTg0Yy01Ljg3NiwxMS43NDcsMi44NjksMjAuNTM3LDEyLjg4MSwyNC4xNzUNCgkJCWMtMC4xNDIsMS4xNjYsMC43MDEsMi41ODcsMi4wODYsMi42ODljMTUuMTgyLDEuMTI0LDI5LjQxMSw1LjU3Nyw0MC42OSwxMy42MTJjMi43OTMsMi4zMzIsNS41NTIsNC43MDMsOC4yNTcsNy4xMzUNCgkJCWM2LjgxNSw3LjA4NiwxMS44ODcsMTYuMDAxLDE0LjQyOCwyNi45MWMzLjE4NiwxMy42NzgsNS4zODksMzAuMTA0LDE0LjE4OCw0MS41NDZjNS4yMTUsNi43OCwxMy4xMTUsNS4wNTQsMTcuOTQ2LTEuMjM1DQoJCQlDMTMyLjQ4MSwxNzcuOTI0LDEzNSwxNjIuNDU2LDEzMS43MzEsMTQ1Ljc5OHogTTU1LjMzNCw3Mi4zMjJjMTUuMzA4LDQuNTkxLDI4Ljc1NiwxNS4wNzksNDAuMTI5LDI1LjkyMg0KCQkJYzcuMDksNi43NjEsMTMuNjk5LDE0LjcyNSwxOC45NTYsMjMuMzdjLTIxLjgxNy0yMC4wNzgtNTEuMDQ3LTMzLjI5NC03My40NjItNTIuMjhDNDUuODQ1LDY5LjY3NCw1MC42OTEsNzAuOTMsNTUuMzM0LDcyLjMyMnoNCgkJCSBNMzUuMjEsNjkuMzk3YzAuNjc0LTAuMDcxLDEuMzQ4LTAuMTAxLDIuMDIzLTAuMTI3YzI1LjM3OCwyNi43NzEsNTkuOTUsNDEuOTM5LDg1LjEyMiw2OC45NzINCgkJCWMxLjU5NCw0LjU5NiwyLjczNSw5LjI4LDMuMjc4LDE0LjAwMWMtMTEuNzYxLTE1LjI3Mi0yOC43NjUtMjguMTYxLTQzLjIyNS00MC4xMzRjLTE3LjY0OC0xNC42MS0zNi45NS0yOC40NDYtNTcuNTc1LTM4LjUwMQ0KCQkJYy0wLjE0NC0wLjA3LTAuMjgyLTAuMDkyLTAuNDItMC4xMTRDMjcuMjkyLDcxLjM3NSwzMC45NzIsNjkuODQzLDM1LjIxLDY5LjM5N3ogTTM0LjA3Nyw5OS4wMTENCgkJCWMtMC4wNjQtMS4zNjctMC43NjQtMi42NDYtMi4zMTUtMi44NjFjLTEwLjQ1MS0xLjQ0Ni0xNC4zODQtNi43MTMtMTMuOTE3LTEyLjIwNWM5LjA2MSw0Ljg4NywxNy44NzMsMTAuMjY2LDI2LjM5NiwxNi4wOTENCgkJCUM0MC44MTIsOTkuNDIxLDM3LjQsOTkuMDUyLDM0LjA3Nyw5OS4wMTF6IE0xMTguMDQyLDE4NS45MzhjLTkuMzQyLDE0LjcwNS0xNi42MTQtMTUuMDYzLTE4LjIyNS0yMS4xOTINCgkJCWMtMC4xMTctMC40NDktMC4yMDMtMC45MDktMC4zMTctMS4zNThjNS44OTYsNy4yNTIsMTIuMzA1LDE0LjA2OSwxOC4yNjYsMjEuMjcxYzAuMjE2LDAuMjYxLDAuNDU1LDAuNDM0LDAuNzAxLDAuNTQyDQoJCQlDMTE4LjMxNywxODUuNDQ1LDExOC4xOTgsMTg1LjY5MywxMTguMDQyLDE4NS45Mzh6IE0xMjAuNTA2LDE4MS41NzRjLTYuNjYtNy4zNzctMTMuNjgxLTE0LjQyLTIwLjQ4Ny0yMS42Ng0KCQkJYy0wLjQ1Mi0wLjQ4LTAuOTc3LTAuNTA5LTEuMzk2LTAuMjkzYy0xLjIxOC01LjI4NC0yLjMyNC0xMC42MDktMy42OTQtMTUuODU0YzkuOTM3LDEwLjc1OSwxOS4wNDIsMjIuMjUzLDI3LjE5NywzNC4zNTkNCgkJCUMxMjEuNjM4LDE3OS4yODEsMTIxLjA4NiwxODAuNDMsMTIwLjUwNiwxODEuNTc0eiBNMTIzLjU5NCwxNzQuMjNjLTkuMDc0LTE0LjMxNy0xOS44MTItMjcuNzg1LTMxLjgyMS00MC4xMg0KCQkJYy02LjA2Ni0xNC4zNzctMjAuNDM5LTI1LjQ1LTM2LjE3Ni0zMS4wNDZjLTExLjgxNS04LjQ0NC0yNC4yNDUtMTUuODg1LTM3LjA1LTIyLjE0NmMwLjgyMy0yLjIwMywyLjMyNS00LjMyMiw0LjMzNS02LjE0OA0KCQkJYy0wLjAxOCwwLjQwOCwwLjE0NiwwLjgyMywwLjYyOCwxLjEwM2MxOS44MTIsMTEuNTE2LDM4LjAwNywyNC44NTksNTUuNjQ0LDM5LjQ5MWMxNi40NzIsMTMuNjY2LDMwLjY5OSwyOS44MTQsNDYuODgxLDQzLjYxMg0KCQkJQzEyNiwxNjQuMDgsMTI1LjI0LDE2OS4xOSwxMjMuNTk0LDE3NC4yM3oiLz4NCgkJPHBhdGggZD0iTTE4OS4zNzMsMTM0LjE0M2MtOC40NDUtNTcuMDgzLTQzLjI5Mi0xMDMuNzEtOTcuNjIyLTEyMy42MjhDNzMuMjU1LDMuNzM0LDQxLjY2Ny04LjcwMywyNi4wMjMsOS4zNzYNCgkJCUMxOC40NTksMTguMTE4LDE0LjkzMiwzOSwyOC40MjgsNDEuODY0YzAuMTczLDAuMzY3LDAuNDY5LDAuNjg0LDAuOTIxLDAuODRjMTguOTgyLDYuNTcxLDM5LjYwMyw2LjE4Niw1OC44MzMsMTIuMzk4DQoJCQljMjMuNDM2LDcuNTcsNDAuNzY0LDMwLjAwOSw0OS45MjksNTEuNzk0YzkuNDUyLDIyLjQ2Nyw0LjEzNCw0Ni40MzksOS43ODIsNjkuNjE0YzMuNzE1LDE1LjIzOCwxNS44NTIsMjAuNTA2LDI5LjcyNSwxNC4wODUNCgkJCUMxOTYuMjg2LDE4MS45NTYsMTkxLjY1NywxNDkuNTgzLDE4OS4zNzMsMTM0LjE0M3ogTTE0OC43MDUsNTcuMjE3YzAuMzY4LDAuNDM1LDAuNjkyLDAuOTE0LDEuMDU1LDEuMzU2DQoJCQljLTkuNzE1LTkuMTIxLTIwLjU2Mi0xNy4zOTgtMzAuMTEtMjYuNjQ5QzEzMC4zNTcsMzguNzUyLDE0MC4wOTQsNDcuMDMsMTQ4LjcwNSw1Ny4yMTd6IE0xMDkuNDcyLDI2LjAxDQoJCQljNy4yMTcsOS4zNzksMTUuNjUsMTcuNTcyLDI0LjI4NiwyNS42NDVjOS43NSw5LjExNiwxOC44MTIsMTkuMDAxLDI4LjU4MywyOC4wNDRjMC40OSwwLjQ1MywxLjA3OCwwLjYwOSwxLjY1MiwwLjU3Ng0KCQkJYzEuMzI1LDIuNTI1LDIuNTk5LDUuMDg1LDMuNzkxLDcuNjgyYy0yNy40MjItMjEuMjI4LTUxLjQ1NS00NS42MDUtNzYuMDk3LTY5LjkxNkM5Ny44NjYsMjAuMzYxLDEwMy43ODEsMjMuMDI3LDEwOS40NzIsMjYuMDF6DQoJCQkgTTg1LjY5OSwxNS44NTFjMC41NzUsMC4xOTMsMS4xMjMsMC40MjIsMS42OTQsMC42MmMyNS4wNjIsMjkuNTU1LDUyLjkzMyw1Ny4zMjIsODQuMTUzLDgwLjMyNQ0KCQkJYzEuMDcxLDIuNzI3LDIuMDk4LDUuNDY1LDMuMDMyLDguMjE4Yy0xMy42MzItMTkuMzk5LTM3LjkwMS0zMy4yODItNTUuNTI4LTQ3LjkyM0MxMDAuNDEyLDQxLjYxLDgwLjkwNiwyMC4xMTYsNTguOTE2LDguMjk0DQoJCQlDNjguMDY1LDkuNzk5LDc3LjIzMSwxMy4wMTMsODUuNjk5LDE1Ljg1MXogTTM1LjAxOCwzOS44MThjMC40NTItMS44NTItMC41MjctNC4wOTctMy4wMTgtNC4xMjkNCgkJCWMtNy42NzgtMC4wOTgtNi42NDctNy43NDMtNC43NjctMTMuNDg5YzguOTI1LDYuNjUzLDE4LjIwOSwxMi43OTksMjcuMjgyLDE5LjI1MUM0OC4wOTIsNDAuNjI3LDQxLjU4LDQwLjA3NywzNS4wMTgsMzkuODE4eg0KCQkJIE02Mi42NjMsNDIuNjg1QzUyLjQ5NywzMy40NjUsMzkuNTk2LDI3LjQxNywyOC4wOCwyMC4xMmMtMC4wMzItMC4wMi0wLjA2LTAuMDE5LTAuMDkyLTAuMDM2DQoJCQljMC41NzctMS40ODksMS4xMzktMi43MjQsMS40ODktMy40MzljMS4yMDMtMi40NTMsMi45MDUtNC4yMzksNC44ODMtNS41OTNjMTcuMzkyLDEzLjAyOSwzNS4zNzgsMjUuMjUzLDUyLjkxLDM4LjA3Ng0KCQkJQzc5LjQyOSw0Ni4yNjksNzEuMTY4LDQ0LjE2OSw2Mi42NjMsNDIuNjg1eiBNMTMxLjU2MSw4MS4yNzZjLTguNjM1LTExLjgwNi0xOS41NTQtMjAuNTA4LTMxLjk0Ni0yNi43OTkNCgkJCUM3OS41NjQsMzguODgyLDU4LjQ5MiwyNC4yODIsMzcuMDY4LDkuNTI4YzMuNDI5LTEuNTA1LDcuMzk3LTIuMDQxLDExLjMwNS0yLjEwNmMxLjc5Ni0wLjAzLDMuNjA0LDAuMDY0LDUuNDE3LDAuMjINCgkJCWMzOC4wMjIsMzguMjksODUuNzMyLDY2LjQ5MiwxMjIuMzU1LDEwNi4zMzVjMC40MTcsMC40NTQsMC45MDksMC41NjYsMS4zNzIsMC40OTVjMC4yODQsMS4wMDYsMC42MDQsMi4wMTUsMC44NywzLjAyDQoJCQljMS43NjUsNi42NjEsMy41NTgsMTQuMTU5LDQuNzI2LDIxLjg1NkMxNjcuODUyLDExNy40NjYsMTUwLjQzNSw5OC41MDgsMTMxLjU2MSw4MS4yNzZ6IE0xNTQuNjQsMTc0LjY0OA0KCQkJYy0xLjQ1Ny00LjIzNC0yLjE4OS04LjcyOC0yLjYxMi0xMy4yNjVjMS44OTYsMi4yMjUsMy44MzEsNC40MSw1Ljc0NCw2LjYxN2M1LjEsNS44ODYsMTAuNTM2LDExLjU1OSwxNi4wNjEsMTcuMDQ1DQoJCQlDMTY2LjYwNiwxODkuMzU5LDE1Ny41ODEsMTgzLjE4OSwxNTQuNjQsMTc0LjY0OHogTTE3OS4xNTMsMTc5LjExOGMtMC42MTgsMS4xNTctMS4yOTcsMi4xMTEtMS45OTksMi45NzcNCgkJCWMtNS41NjYtNS41ODktMTEuNDA5LTEwLjkwNS0xNy4wMjItMTYuNDVjLTIuNTctMi41MzktNS4yNjEtNS41Ny04LjM2My03LjU4OWMtMC4yLTMuMTI0LTAuMzAxLTYuMjQ0LTAuNDI0LTkuMjkNCgkJCWMtMC4wMDktMC4yMjItMC4wMjEtMC40My0wLjAzMS0wLjY0OWM0LjYxMSw0LjQwOCw5LjE0Niw4Ljg1NCwxMy40MjQsMTMuNjEyYzUuMDMzLDUuNTk0LDkuMzE3LDExLjgzOCwxNC40MjYsMTcuMzcNCgkJCUMxNzkuMTYxLDE3OS4xMDQsMTc5LjE1OCwxNzkuMTExLDE3OS4xNTMsMTc5LjExOHogTTE4MS4xNiwxNzQuNjE1Yy04LjA1MS0xMC45MzItMTguOTczLTIxLjIxMi0yOS45NzEtMjkuMDk2DQoJCQljLTAuMjYzLTUuNTMzLTAuNjQ2LTEwLjc1Ny0xLjI3OC0xNS44NzhjNS41NTYsNS45NDcsMTAuOTM4LDEyLjA0NiwxNi4zMDcsMTguMTY3YzQuODIyLDUuNDk3LDEwLjU1NCwxNC4yNTksMTcuMzU3LDE3LjU4Mw0KCQkJQzE4My4wNTEsMTY4LjU3NCwxODIuMjgyLDE3MS42NzIsMTgxLjE2LDE3NC42MTV6IE0xODQuMjMyLDE1OS42NTJjLTMuODQyLTQuNjczLTkuNDUzLTguNzg2LTEzLjQ4MS0xMi43NzUNCgkJCWMtNy4xNTItNy4wODItMTQuMTkxLTE0LjI4LTIxLjQxMy0yMS4yOTFjLTEuMjk1LTguMzY1LTMuNDA0LTE2LjU2My03LjEyNy0yNS40MTNjLTAuNDgyLTEuMTQ0LTEuMDIzLTIuMjEyLTEuNTM2LTMuMzE2DQoJCQljMTMuNjMsMTYuNDA1LDI2LjExNiwzMy45MDksNDIuNDc0LDQ3LjAyYzAuMTg0LDAuMTQ3LDAuMzkzLDAuMjE5LDAuNjA1LDAuMjU5QzE4NC4zMzQsMTQ5LjMzNywxODQuNTc3LDE1NC41NzIsMTg0LjIzMiwxNTkuNjUyeg0KCQkJIi8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+");
                
                }
            </style>
            <div class="social-links-container">
            <div class="rss-icon"></div>
                ${renderedLinks.join('')}
            </div>
        `;
    }

    assertFields(links) {
        if (!Array.isArray(links)) {
            console.error('The social links must be an array');
        }
        links.forEach(link => {
            if (!link.name || !link.url) {
                console.error('The social links must have a name and a url');
            }
        })
    }
}

window.customElements.define('social-links', SocialLinks);