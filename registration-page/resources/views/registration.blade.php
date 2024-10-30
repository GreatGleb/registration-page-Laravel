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

            #image-container {
                width: 200px;
                height: 200px;
                border: 1px solid #ccc;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #image-preview {
                width: 100%;
                height: 100%;
                object-fit: cover;
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

        <h1>Registration form</h1>

        <form id="image-upload-form" method="POST" enctype="multipart/form-data" action="{{ route('register') }}">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name"><br><br>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email"><br><br>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone"><br><br>
            <label for="position">Position:</label>
            <select id="position">
            </select><br><br>
            <input type="file" id="file-input" accept=".jpg, .jpeg"><br><br>
            <div id="image-container">
                <img id="image-preview" src="" alt="Preview image">
            </div>
            <button type="submit">Отправить</button>
        </form>

        <script>
            let imagePreview = document.getElementById('image-preview');

            window.onload = async () => {
                if (document.getElementById('file-input').files && document.getElementById('file-input').files[0]) {
                    imagePreview.src = await getPhotoBase64Url(document.getElementById('file-input').files[0])
                }

                getUser(5)
                setPositons()
            }

            document.getElementById('file-input').addEventListener('change', async (e) => {
                const file = e.target.files[0];
                imagePreview.src = await getPhotoBase64Url(file)
            });

            document.getElementById('image-upload-form').addEventListener('submit', async (e) => {
                e.preventDefault();

                let container = document.getElementById('image-upload-form')

                let xhr = new XMLHttpRequest();

                let token = await getToken()

                xhr.open("POST", '{{ route('register') }}')
                xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
                xhr.setRequestHeader('Accept', 'application/json;');
                xhr.setRequestHeader('Authorization', 'Bearer ' + token);

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
                };

                let photo = container.querySelector('#file-input')
                if(photo.files && photo.files[0]) {
                    photo = photo.files[0]
                    photo = await getPhotoBase64Url(photo);  // file from input
                } else {
                    photo = ''
                }

                let json = {
                    name: container.querySelector('#name').value,
                    email: container.querySelector('#email').value,
                    phone: container.querySelector('#phone').value,
                    position_id: Number(container.querySelector('#position').value),
                    photo: photo
                };

                json = JSON.stringify(json);

                xhr.send(json);
            });

            function getPhotoBase64Url(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.readAsDataURL(file);

                    reader.onload = () => resolve(reader.result)
                    reader.onerror = error => reject(error)
                })
            }

            function getToken() {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();

                    xhr.open("GET", '{{ route('token') }}')
                    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

                    xhr.onload = function () {
                        let result = xhr.response
                        result = JSON.parse(result)
                        resolve(result.plainTextToken)
                    };

                    xhr.send();
                })
            }

            function getUser(id) {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();

                    let route = '{{ route('getUser',  ":id") }}'
                    route = route.replace(':id', id);

                    xhr.open("GET", route)
                    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

                    xhr.onload = function () {
                        let result = xhr.response
                        result = JSON.parse(result)
                        resolve(result.plainTextToken)
                    };

                    xhr.send();
                })
            }

            function getPositions() {
                return new Promise((resolve, reject) => {
                    let xhr = new XMLHttpRequest();

                    let route = '{{ route('positions') }}'

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

            async function setPositons() {
                let positions = await getPositions()

                if(positions.success) {
                    let listPositions = document.querySelector('#position')

                    for(let pos of positions.positions) {
                        let newOption = document.createElement('option')
                        newOption.value = pos.id
                        newOption.innerText = pos.position

                        listPositions.append(newOption)
                    }
                }
            }
        </script>
    </body>
</html>
