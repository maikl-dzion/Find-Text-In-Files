<?php

    $requestUri = trim($_SERVER['REQUEST_URI'], '/');
    $uriArray = explode('/', $requestUri);
    array_pop($uriArray);
    $apiUrl = '/' . implode('/', $uriArray) . '/api/index.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <!-- Css Resource -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">

    <!-- Js Resource -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <!--    <script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>-->

    <script src="http://bolderfest.ru/ARCHIVES/lg.js"></script>

    <link href="assets/css/base.css" rel="stylesheet" >
    <link href="assets/css/style.css" rel="stylesheet" >

    <style></style>

    <script>
        const ApiUrl = '<?php echo $apiUrl; ?>';
    </script>

</head>
<body>

<div id="app">

    <nav>
        <div class="container">
            <h1 style="font-size: 17px; font-style: italic" >Поисковый сервис</h1>
            <div id="menu"  ></div>
            <a id="menu-toggle" href="#" class=" ">&#9776;</a>
        </div>
    </nav>

    <header style="padding:0px; margin: 0px;">
        <div class="container">
            <template>
                <div v-if="preloader" class="text-center">
                    <v-progress-circular
                            indeterminate
                            color="primary"
                    ></v-progress-circular>
                </div>
            </template>
        </div>
    </header>

    <!---- SUB HEADER       -------->
    <v-container>
        <v-row align="center" justify="space-around" style="padding:0px !important; margin:0px !important;">

            <v-col cols="12" sm="12" md="12">
                <v-text-field v-model="apiUrl" label="Путь до поискового файла"
                              style="color:red; background: ghostwhite; border:gainsboro 1px solid"></v-text-field>
            </v-col>

            <v-btn @click="getServerDir()">Директория Apache</v-btn>
            <v-btn @click="getSystemDir()" color="green">Директория Системы</v-btn>
            <v-btn @click="clearResult()" color="green">Очистить результаты</v-btn>

            <v-btn @click="updateElemCss('.container', {'width' : '30%'})" color="green"> cont 30</v-btn>
            <v-btn @click="updateElemCss('.container', {'width' : '100%'})" color="green">cont 100</v-btn>
            <v-btn @click="updateElemCss('.container', {'width' : '50%'})" color="green">cont 50</v-btn>
            <v-btn @click="testAction()" color="green">Тестируем компоненты</v-btn>

        </v-row>
    </v-container>
    <!---- / SUB HEADER  ----------->


    <!------ ОСНОВНАЯ ЧАСТЬ СТРАНИЦЫ  --------->
    <v-app>
        <v-main>
            <v-container>

                <div class="main">

                    <!------ ДЕРЕВО КАТЕГОРИЙ (ЛЕВАЯ ПАНЕЛЬ) ----->
                    <div class="left-panel">
                        <div v-for="(item, name) in dirList" :key="name">
                            <tree-view-template
                                    :name="name"
                                    :item="item">
                            </tree-view-template>
                        </div>
                    </div>

                    <!------ ПРАВАЯ ПАНЕЛЬ ---------->
                    <div class="content-panel">
                        <div>
                            <div v-if="timerDate">{{timerDate}}</div>
                            <pre v-if="timerInfo.start" >
                                {{timerInfo}}
                            </pre>

                        </div>
                        <!------ ПОИСКОВЫЙ КОНТЕЙНЕР  ----->
                        <div class="search-form-container">

                            <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid">
                                <v-col cols="12" sm="12" md="12">
                                    <v-text-field v-model="textSearch" label="Строка поиска"
                                                  style="color:red; background: ghostwhite; border:gainsboro 1px solid"></v-text-field>
                                </v-col>

                                <v-col cols="12" sm="12" md="12">
                                    <v-text-field v-model="pathSearch" label="Директория поиска"
                                                  style="color:red; background: ghostwhite; border:gainsboro 1px solid"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid">
                                <v-col cols="12" sm="12" md="12">
                                    <div style="display: flex;">
                                        <v-btn @click="findTextToFiles()"
                                               style="background: green !important; color:white;margin:3px; width:50%;">
                                            Поиск по содержимому в файлах
                                        </v-btn>
                                        <v-btn style="background: orange; color:white; margin:3px; width:50%;">Поиск по
                                            имени файла
                                        </v-btn>
                                    </div>

                                    <div style="display: flex;">
                                        <v-btn style="background: lightseagreen; color:white;margin:3px; width:50%;">
                                            Поиск по имени папки
                                        </v-btn>
                                        <v-btn style="background: royalblue; color:white;margin:3px; width:50%;"
                                               @click="catalogUpdate()">Обновить каталог
                                        </v-btn>
                                    </div>
                                </v-col>
                            </v-row>

                        </div>
                        <!---- / ПОИСКОВЫЙ КОНТЕЙНЕР ------->

                        <template>
                            <div>
                                <!------ Кнопки переключения панелей ------>
                                <div class="v-item-group theme--dark v-slide-group v-tabs-bar cyan--text" >
                                    <div class="v-slide-group__wrapper">
                                        <div class="v-slide-group__content v-tabs-bar__content">
                                            <div @click="panelActive = 'find-result'"  style="border-bottom:2px solid #ffeb3b" class="v-tab tab-panel-button" >Результаты поиска</div>
                                            <div @click="panelActive = 'file-content'" class="v-tab tab-panel-button" >Содержимое файла</div>
                                            <div @click="panelActive = 'error-view'"   class="v-tab tab-panel-button" >Показать Info и ошибки</div>
                                        </div>
                                    </div>
                                </div><div style="border: 0px solid gainsboro; margin:5px 0px 5px 0px"></div>

                                <!------ Результаты поиска ------>
                                <div v-if="panelActive == 'find-result'">
                                    <div v-for="(item, path) in resultList">
                                        <div @click="getFileContent(path)" style="font-weight: bold; padding:4px; border:1px gainsboro solid; cursor:pointer">{{path}}</div>
                                        <find-result-view
                                                style="margin-left:18px;"
                                                :list="item"
                                                :path="path"
                                        ></find-result-view>
                                    </div>
                                </div>
                                <!------ Содержимое файла ------>
                                <div v-else-if="panelActive == 'file-content'">
                                    <div v-for="(value) in fileContentList">
                                        <pre>{{value}}</pre>
                                    </div>
                                </div>
                                <!------ Показать ошибки ------>
                                <div v-else-if="panelActive == 'error-view'">
                                     <div style="border: 1px solid #26abe2; color: #007eb2; padding:5px; margin:5px" >
                                         <div> SERVER INFO </div>
                                         <pre >{{_httpInfo}}</pre>
                                     </div>

                                    <div style="border: 1px solid red; color: red; padding:5px;  margin:5px" >
                                         <div> SERVER ERROR </div>
                                         <pre > {{_responseError}}</pre>
                                    </div>

                                    <div style="border: 1px solid green; color: green; padding:5px; margin:5px" >
                                     <div> RESPONSE DATA </div>
                                     <pre >{{_responseData}}</pre>
                                    </div>
                                </div>

                            </div>
                        </template>

                    </div>

                </div>

            </v-container>
        </v-main>
    </v-app>
    <!------ / ОСНОВНАЯ ЧАСТЬ СТРАНИЦЫ  -------->

</div><!---- #app ---->

<!---------------------------->
<!--- ШАБЛОНЫ КОМПОНЕНТОВ ---->

<script type="text/x-template" id="find-result-view-template">

    <div class="result-container">
        <v-expansion-panels>
            <v-expansion-panel v-for="(item, num) in list" :key="num">
                <v-expansion-panel-header >

                    <div style="text-align: left;" >
                        <span style="color:saddlebrown; margin-right:10px;">{{num}}</span> <span style="color: green" >{{item.row}}</span>
                    </div>

                </v-expansion-panel-header>
                <v-expansion-panel-content style="padding:0px !important;">
                    <table class="result-table-container">
                        <tr>
                            <td>Строка</td>
                            <td>{{item.row}}</td>
                        </tr>
                        <tr>
                            <td>Путь</td>
                            <td>{{item.path}}</td>
                        </tr>
                        <tr>
                            <td>Номер строки</td>
                            <td>{{item.num}}</td>
                        </tr>
                    </table>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-expansion-panels>
    </div>

</script>


<script type="text/x-template" id="tree-view-template">

    <div class="tree-container">
        <div style="cursor: pointer;">
            <table>
                <template v-if="type == 'dir'">
                    <tr @click="getChildDir()">
                        <td><img src="assets/img/folder_icon.png" style="width:15px;"></td>
                        <td>{{name}}</td>
                    </tr>
                </template>
                <template v-else>
                    <tr @click="getFileContent(path, 'arr')" >
                        <td><img src="assets/img/file_icon.png" style="width:15px;"></td>
                        <td>{{name}}</td>
                    </tr>
                </template>
            </table>
        </div>

        <div v-if="openChildDir" class="child-container" style="margin-left:18px;">
            <div v-for="(item, name) in childList" :key="name" >
                <tree-view-template
                        :name="name"
                        :item="item"
                ></tree-view-template>
            </div>
        </div>

    </div>

</script>

<script src="assets/js/components.js"></script>
<script src="assets/js/app.js"></script>

</body>
</html>
