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
        <h1>Testing API</h1>

        <div>
            <a href="{{ route('registration') }}">Registration</a>
        </div>
        <div>
            <a href="{{ route('users') }}">Users</a>
        </div>

        <h1>List of users</h1>

        <table class="users">
            <tr>
                <th>photo</th>
                <th>name</th>
                <th>email</th>
                <th>phone</th>
                <th>position</th>
                <th>registration date</th>
            </tr>
            <tr class="user empty d-none">
                <td class="photo">
                    <a href="{{ route('user', ':id') }}">
                        <img alt="user photo">
                    </a>
                </td>
                <td class="name">
                    <a href="{{ route('user', ':id') }}">
                        name
                    </a>
                </td>
                <td class="email">
                    <a href="{{ route('user', ':id') }}">
                        email
                    </a>
                </td>
                <td class="phone">
                    <a href="{{ route('user', ':id') }}">
                        phone
                    </a>
                </td>
                <td class="position">
                    <a href="{{ route('user', ':id') }}">
                        position
                    </a>
                </td>
                <td class="registration_date">
                    <a href="{{ route('user', ':id') }}">
                        registration date
                    </a>
                </td>
            </tr>
        </table>

        <button>Show more</button>

        <script>
            let nextLink
            let button = document.querySelector('button')
            button.addEventListener('click', setUsers)

            window.onload = async () => {
                setUsers()
            }

            function getUsers(link, count) {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();

                    let route = '{{ route('getUsers') }}'


                    if(link) {
                        route = link
                    } else if(count) {
                        route += '?count=' + count
                    }

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

            async function setUsers() {
                let users = await getUsers(nextLink, 0)
                nextLink = users.links.next_url

                if(!nextLink) {
                    button.classList.add('d-none')
                }

                if(users.success) {
                    let listUsers = document.querySelector('.users')
                    let emptyUserRow = listUsers.querySelector('.user.empty')

                    for(let user of users.users) {
                        let newUser = emptyUserRow.cloneNode(true);

                        newUser.querySelector('.photo a').href = newUser.querySelector('.photo a').href.replace(':id', user.id)
                        newUser.querySelector('.name a').href = newUser.querySelector('.name a').href.replace(':id', user.id)
                        newUser.querySelector('.email a').href = newUser.querySelector('.email a').href.replace(':id', user.id)
                        newUser.querySelector('.phone a').href = newUser.querySelector('.phone a').href.replace(':id', user.id)
                        newUser.querySelector('.position a').href = newUser.querySelector('.position a').href.replace(':id', user.id)
                        newUser.querySelector('.registration_date a').href = newUser.querySelector('.registration_date a').href.replace(':id', user.id)

                        newUser.querySelector('.photo img').src = user.photo
                        newUser.querySelector('.name a').innerText = user.name
                        newUser.querySelector('.email a').innerText = user.email
                        newUser.querySelector('.phone a').innerText = user.phone
                        newUser.querySelector('.position a').innerText = user.position

                        let datetime = new Date(user.registration_timestamp * 1000).toLocaleString()
                        newUser.querySelector('.registration_date a').innerText = datetime

                        newUser.classList.remove('empty')
                        newUser.classList.remove('d-none')

                        listUsers.append(newUser)
                    }
                }
            }
        </script>

    </body>
</html>
