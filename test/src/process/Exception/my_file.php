<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="http://bolderfest.ru/ARCHIVES/lg.js"></script>

    <style>
       .btn {
           padding:10px;
           margin :10px;
           width: 50%;
       }
    </style>

</head>

<body>

<div class="container" id="test_id_2345666TYkyi uitnb67766_nbjgh" >

    <div class="row" style="padding:8px;">

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="register()" class="btn"> Сохранить </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="userUpdate()" class="btn"> Обновить </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_auth" ></form>
                <button onclick="authInit()" class="btn" > Сохранить </button>
            </div>
        </div>


        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="deleteUser()" class="btn"> Удалить </button>
            </div>
        </div>


        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="addGroup()" class="btn"> Добавить новую группу </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="updateGroup()" class="btn"> Изменить группу </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="deleteGroup()" class="btn"> Удалить группу </button>
            </div>
        </div>


        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="addChild()" class="btn"> Добавить ребенка </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="updateChild()" class="btn"> Изменить ребенка </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="deleteChild()" class="btn"> Удалить ребенка </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid">
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="getDataList('get_groups')" class="btn"> Группы </button>
            </div>
        </div>

        <div class="col-md-6" style="border:1px gainsboro solid" >
            <div class="t1" >
                <form id="user_register" ></form>
                <button onclick="getDataList('get_children')" class="btn"> Дети </button>
            </div>
        </div>

    </div>

    <script>

        const ApiUrl = 'http://178.249.68.43/basisportal/api/api.php';

        let form  = document.querySelector('#user_register');
        let formAuth  = document.querySelector('#user_auth');
        var roles = [];


        let registerUrl = 'http://178.249.68.43/basisportal/api/api.php/user_register';     // POST
        let updateUrl   = 'http://178.249.68.43/basisportal/api/api.php/user_update';       // POST
        let authUrl     = 'http://178.249.68.43/basisportal/api/api.php/user_auth';         // POST

        let deleteUrl   = 'http://178.249.68.43/basisportal/api/api.php/user_delete';       // POST

        let logoutUrl        = 'http://178.249.68.43/basisportal/api/api.php/user_logout/user_id'; // GET
        let getRoleUrl   = 'http://178.249.68.43/basisportal/api/api.php/get_role_list';           // GET
        let getGardenListUrl = 'http://178.249.68.43/basisportal/api/api.php/get_garden_list';     // GET
        let getUsersListUrl  = 'http://178.249.68.43/basisportal/api/api.php/get_users_list';      // GET


        let addGroupUrl    =  ApiUrl + '/add_group';     // POST
        let updateGroupUrl =  ApiUrl + '/update_group';  // POST
        let deleteGroupUrl =  ApiUrl + '/delete_group';  // GET

        let addChildUrl    =  ApiUrl + '/add_child';     // POST
        let updateChildUrl =  ApiUrl + '/update_child';  // POST
        let deleteChildUrl =  ApiUrl + '/delete_child';  // GET

        let user = {
            user_id    : 2,
            username   : 'Иванов',
            surname    : 'Сергей',
            patronymic : 'Викторович',
            email      : 'dzion67@mail.ru',
            login      : 'serg',
            password   : '1234',
            role_id    : 2,
            garden_id  : 1,
            phone      : '89074567890',
            groups     : [2,4,5],
        };

        let user22 = {
            user_id    : 44,
            username   : 'Иванов',
            surname    : 'Сергей',
            patronymic : 'Викторович',
            email      : 'dzion6777777@mail.ru',
            login      : 'serg',
            password   : '1234',
            role_id    : 2,
            garden_id  : 1,
            phone      : '8907456567777',
            groups     : [2,3,4,5],
        };

        let auth = {
            email    : 'dzion67@mail.ru',
            password : '1234',
        }


        let newGroup = {
            group_id    : 0,
            group_name  : 'Младшая группа 1',
            user_id     : 37,
            dt_create   : '',
            dt_update   : '',
            educator_id : 42,
            note        : 'младшая группа дошкольников',
        };

        let editGroup = {
            group_id    : 2,
            group_name  : 'Старшая группа 2',
            user_id     : 37, // кто создает
            dt_create   : '',
            dt_update   : '',
            educator_id : 42, // воспитатель
            note        : 'Дошкольная группа',
        };


        let newChild = {
            child_id    : 0,
            child_name    : 'Иван',
            child_family  : 'Селиванов',
            group_id    : 1,
            parent1_id  : 0,
            parent2_id  : 0,

            user_id     : 37,
            dt_create   : '',
            dt_update   : '',
        };

        let editChild = {
            child_id    : 4,
            child_name    : 'Николай',
            child_family  : 'Иванов',
            group_id    : 3,
            parent1_id  : 0,
            parent2_id  : 0,

            user_id     : 37,
            dt_create   : '',
            dt_update   : '',
        };


        function deleteUser() {
            let userId = 45;
            fetchData(deleteUrl + '/' + userId)
                .then((data) => {lg(data)});
        }

        function addGroup() {
            postData(addGroupUrl, newGroup)
                .then((data) => {
                   console.log(data);
                });
        }

        function updateGroup() {
            postData(updateGroupUrl, editGroup)
                .then((data) => {
                  console.log(data);
                });
        }

        function deleteGroup() {
            fetchData(deleteGroupUrl + '/2')
                .then((data) => {
                    console.log(data);
             });
        }

        function addChild() {
            postData(addChildUrl, newChild)
                .then((data) => {
                console.log(data);
        });
        }

        function updateChild() {
            postData(updateChildUrl, editChild)
                .then((data) => {
                   console.log(data);
            });
        }

        function deleteChild() {
            fetchData(deleteChildUrl + '/3')
                .then((data) => {
                console.log(data);
            });
        }

        function getDataList(url, id = '') {
            fetchData(ApiUrl + '/' + url)
                .then((data) => {
                    console.log(data)
                });
        }

        // fetchData(getRoleUrl)
        //     .then((data) => {
        //         roles = data
        //         let select = createRoles(roles)
        //         form.append(select);
        //     });


        for(let fname in auth) {
            let v = auth[fname];
            let formElem = createElem('auth-' + fname, v);
            formAuth.append(formElem);
        }


        for(let fname in user) {
            let v = user[fname];
            let formElem = createElem(fname, v);
            form.append(formElem);
        }

        //////////////////////////////////////////
        //////////////////////////////////////////


        function createElem(name, value = '') {
            let div = document.createElement("div");
            div.innerHTML = `
                <div class="form-group" >
                    <label>${name}</label>
                    <input name="${name}"
                           type="text" id="${name}"
                           class="form-control" value="${value}" >
                </div>
            `;
            return div;
        }

        function createRoles(roles) {
            let select  = document.createElement("select");
            let options = '';
            for(let i in roles) {
                let role = roles[i];
                options += `<option value="${role.role_id}" >${role.role_name}</option>`;
            }

            select.name = 'select_role_id';
            select.id   = 'select_role_id';
            select.innerHTML = options;
            select.onchange = function(e) {
                let s = e.target.selectedIndex;
                s++;
                r = document.querySelector('input#role_id');
                r.value = s;
            }
            return select;
        }

        function userUpdate() {
            // debugger;
            // alert('yuuuu');
            postData(updateUrl, user22)
                .then((data) => {
                    // alert('yuuuu');
                    lg(data)
                    // console.log(data); // JSON data parsed by `response.json()` call
                });
        }

        function register() {

            for(let fname in user) {
                let inp = document.querySelector('input#' + fname);
                user[fname] = inp.value;
            }

            user['groups'] = [2, 4, 5]
            // lg(user);

            postData(registerUrl, user)
                .then((data) => {
                    lg(data)
                    // console.log(data); // JSON data parsed by `response.json()` call
                });
        }

        function authInit() {

            for(let fname in auth) {
                let inp = document.querySelector('input#auth-' + fname);
                auth[fname] = inp.value;
            }

            postData(authUrl, auth)
                .then((data) => {
                    localStorage.setItem('deti-token', data.result.token)
                    // lg(data)
                    // console.log(data); // JSON data parsed by `response.json()` call
                });
        }


        function checkToken() {

            fetchData(getUsersListUrl)
                .then((data) => {
                    // localStorage.setItem('deti-token', data.result.token)
                    lg(data)
                    // console.log(data); // JSON data parsed by `response.json()` call
                });
        }

        // Пример отправки POST запроса:
        async function postData(url = '', data = {}) {
            // Default options are marked with *
            // debugger;

            const response = await fetch(url, {
                method : 'POST', // *GET, POST, PUT, DELETE, etc.
                mode   : 'cors'   , // no-cors, *cors, same-origin
                cache  : 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                credentials: 'same-origin', // include, *same-origin, omit
                headers: {
                    'Content-Type': 'application/json',
                    // 'Content-Type': 'application/x-www-form-urlencoded',
                },
                redirect      : 'follow', // manual, *follow, error
                referrerPolicy: 'no-referrer', // no-referrer, *client
                body: JSON.stringify(data)     // body data type must match "Content-Type" header
            });

            return await response.json(); // parses JSON response into native JavaScript objects
        }


        // Пример отправки POST запроса:
        async function fetchData(url = '') {
            // Default options are marked with *
            // localStorage.setItem('deti-token', 'fhfgfhfhhfhfhfhfh');
            var token = localStorage.getItem('deti-token');
            const response = await fetch(url, {
                method : 'GET', // *GET, POST, PUT, DELETE, etc.
                mode   : 'cors'   , // no-cors, *cors, same-origin
                cache  : 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                credentials: 'same-origin', // include, *same-origin, omit
                headers: {
                    'Content-Type'    : 'application/json',
                    'User-Jwt-Token'  : token,
                    // 'Content-Type': 'application/x-www-form-urlencoded',
                },
                redirect      : 'follow', // manual, *follow, error
                referrerPolicy: 'no-referrer', // no-referrer, *client
                // body: JSON.stringify(data)     // body data type must match "Content-Type" header
            });

            return await response.json(); // parses JSON response into native JavaScript objects
        }

    </script>

</div><!-- /.container -->

</body></html>