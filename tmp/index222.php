








<!DOCTYPE html>
<html>
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet">
<!--    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>

    <link href="style.css" rel="stylesheet">

</head>
<body>

<div id="app" class="wrapper">

    <div v-if="preloader" class="preloaderContainer">
        <h3 style="width:15%; margin: 12% auto 0px auto; color:white; font-size: 28px;">Идет поиск ...</h3>
    </div>

    <nav>
        <div class="nav-wrapper">
            <a href="#" class="brand-logo right">Поиск по файлам</a>
        </div>
    </nav>


    <ul class="collapsible">
        <li>
            <div class="collapsible-header">
                <i class="material-icons">filter_drama</i>
                First
                <span class="new badge">4</span></div>
            <div class="collapsible-body"><p>Lorem ipsum dolor sit amet.</p></div>
        </li>
        <li>
            <div class="collapsible-header">
                <i class="material-icons">place</i>
                Second
                <span class="badge">1</span></div>
            <div class="collapsible-body"><p>Lorem ipsum dolor sit amet.</p></div>
        </li>
    </ul>

    <div class="row">
        <form class="col s12">
            <div class="row">
                <div class="input-field col s6">
                    <input placeholder="Placeholder" id="first_name" type="text" class="validate">
                    <label for="first_name">First Name</label>
                </div>
                <div class="input-field col s6">
                    <input id="last_name" type="text" class="validate">
                    <label for="last_name">Last Name</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input disabled value="I am not editable" id="disabled" type="text" class="validate">
                    <label for="disabled">Disabled</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" type="password" class="validate">
                    <label for="password">Password</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="email" type="email" class="validate">
                    <label for="email">Email</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    This is an inline input field:
                    <div class="input-field inline">
                        <input id="email_inline" type="email" class="validate">
                        <label for="email_inline">Email</label>
                        <span class="helper-text" data-error="wrong" data-success="right">Helper text</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!--- Панель управления --->
    <div class="controlPanel" style="">
        <div style="width:80%; margin:0px auto 10px auto; border:0px gainsboro solid; padding:5px;">

            <div style="margin:3px">
                <div style="padding:3px; font-size:14px; font-style:italic">Поисковое поле: </div>
                <div style="padding:3px; font-size:14px;">
                    <input v-model="textValue" type="text" placeholder="поиск ..."
                           style="width:300px; border-radius:0px; height:30px; border: 1px gainsboro solid; padding:0px 0px 0px 4px;">
                </div>
            </div>

            <hr>
            <div style="display:flex; margin:3px">
                <div style="padding:3px; font-size:14px; font-style:italic">Ищем в директории : </div>
                <div style="padding:3px; font-size:14px;color:gold">{{pathname}}</div>
            </div>
            <hr>
            <div style="display:flex; margin:3px">
                <div style="padding:3px; font-size:14px; font-style:italic">По значению : </div>
                <div style="padding:3px; font-size:14px;color:gold">{{textValue}}</div>
            </div>
            <hr>
            <div style="margin:3px">
                <div class="btn-cls" @click="findText">Выполнить</div>
            </div>

        </div>
    </div>

    <!--- Выбор директории --->
    <!--        <div style="width:80%; margin:10px auto 10px auto; padding:5px;">-->
    <!--            <div class="btn-cls" @click="openDirList">Открыть / закрыть</div>-->
    <!--            <div v-if="dirListFlag">-->
    <!--              <div v-for="(path, dirName) in serverFiles"-->
    <!--                 @click="setPath(path, dirName)" class="dirFilesBox" >{{dirName}}</div>-->
    <!--            </div>-->
    <!--        </div><hr>-->

    <!--- Выбор директории --->
    <div style="width:80%; margin:10px auto 10px auto; padding:5px;">
        <div style="display: flex; border:0px gainsboro solid">
            <h5 style="width:200px; padding:3px; font-size:18px; font-style:italic; color:grey">
                Список директорий
            </h5>
            <div class="btn-cls" @click="openDirList">Открыть / закрыть</div>
        </div>

        <ul v-if="dirListFlag" style="margin:5px 0px 5px 0px">
            <li v-for="(path, dirName) in serverFiles"
                style="display: inline-flex; margin:3px; padding:3px; border:1px grey solid; color:darkblue; font-weight: bold"
                @click="setPath(path, dirName)" class="dirFilesBox">
                <div class="liDirItem">{{dirName}}</div>
            </li>
        </ul>
    </div>
    <hr>

    <div style="height: 40px; background: #37393c; padding:6px 0px">
        <h5 style="width:80%; margin:0px auto 0px auto;padding:3px; font-size:18px; font-style:italic; color:white">
            Показ результатов поиска :
        </h5>
    </div>


    <!--- Показ результата --->
    <div style="width:80%; margin:10px auto 10px auto; padding:5px;">
        <div @click="showToggle('preShowFlag')" class="btn-cls">Показ доп. полей</div>
        <br><br>

        <div v-for="(item, path) in resultFiles"
             @click="getFileContent(path, item)" class="resultsContainer"
             style="margin:0px; padding:0px;">
            <div class="file-path-item">{{path}}</div>
            <div v-for="(values, name) in item" class="resultItem"
                 style="margin:10px; padding:0px;">
                <div style="display: flex">
                    <div>Номер строки:</div>
                    <div>{{values.num}}</div>
                </div>
                <div v-if="preShowFlag" class="pre-item-result">
                    <pre>{{values}}</pre>
                </div>
            </div>
        </div>
    </div>

    <pre>
        {{documentRoot}}
    </pre>

</div>

<script src="app.js"></script>
</body>
</html>