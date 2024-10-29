<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }

            .d-none {
                display: none;
            }

            img {
                max-width: 70px;
                max-height: 70px;
            }
        </style>
    </head>
    <body class="antialiased">
        <h1>Page of user</h1>

        <img src="" alt="" class="photo">
        <h3 class="name_title">Name</h3>
        <p class="name"></p>
        <h3 class="email_title">Email</h3>
        <p class="email"></p>
        <h3 class="phone_title">Phone</h3>
        <p class="phone"></p>
        <h3 class="position_title">Position</h3>
        <p class="position"></p>

        <script>
            window.onload = async () => {
                setUser({{ $id }})
            }

            function getUser(id) {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();

                    let route = '{{ route('getUser', ':id') }}'
                    route = route.replace(':id', id)

                    xhr.open("GET", route)
                    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

                    xhr.onload = function () {
                        let result = xhr.response
                        result = JSON.parse(result)

                        if(result.message) {
                            alert(result.message)
                        }

                        if(result.fails) {
                            for(let errors in result.fails) {
                                for(let error in result.fails[errors]) {
                                    alert(result.fails[errors][error])
                                }
                            }
                        }

                        resolve(result)
                    };

                    xhr.send();
                })
            }

            async function setUser(id) {
                let user = await getUser(id)

                if(user.success) {
                    document.querySelector('img').src = user.user.photo
                    document.querySelector('.name').innerText = user.user.name
                    document.querySelector('.email').innerText = user.user.email
                    document.querySelector('.phone').innerText = user.user.phone
                    document.querySelector('.position').innerText = user.user.position
                }
            }
        </script>
    </body>
</html>
