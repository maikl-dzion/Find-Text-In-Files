<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="http://bolderfest.ru/ARCHIVES/lg.js"></script>

    <link href="assets/style.css" rel="stylesheet">

    <style></style>

</head>
<body>
<div id="app">

    <v-toolbar>
        <v-toolbar-title>Поиск текста по файлам</v-toolbar-title>
    </v-toolbar>

    <!------ SEARCH CONTAINER ------->
    <template>
        <v-form><v-container>
            <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid" >
                <v-col cols="4" sm="4" md="3" >
                    <v-text-field
                        v-model="textSearch"
                        label="Строка поиска" >
                    </v-text-field>
                </v-col>

                <v-col cols="8" sm="8" md="9" >
                    <v-text-field
                        v-model="pathSearch"
                        label="Директория поиска" >
                    </v-text-field>
                </v-col>
            </v-row>

                <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid" >
                    <v-col cols="4" sm="4" md="3" >
                        <v-text-field
                                v-model="apiUrl"
                                label="Путь до поискового файла" >
                        </v-text-field>
                    </v-col>

                    <v-col cols="4" sm="4" md="3" >
                        <div style="display: flex;" >
                            <v-btn @click="findTextToFiles()" >Поиск по содержимому в файлах</v-btn>
                            <v-btn >Поиск по имени файла</v-btn>
                            <v-btn >Поиск по имени папки</v-btn>
                            <v-btn @click="catalogUpdate()" >Обновить каталог</v-btn>
                        </div>
                    </v-col>

                </v-row>

        </v-container></v-form>
    </template>
    <!------ / SEARCH CONTAINER ------->

    <hr>

    <!---- SUB HEADER -------->
    <template><v-container>
        <v-row align="center" justify="space-around" style="padding:0px !important;" >

            <v-btn @click="setDir('apache')" >Директория Apache</v-btn>
            <v-btn @click="setDir('system')" color="green">Директория Системы</v-btn>
            <v-btn @click="clearResult()" color="green">Очистить результаты</v-btn>

            <div style="margin-top:-10px; margin-left:20px;" >
                <table>
                    <tr><td>Строка</td>
                        <td><div style="font-weight: bold; color: green; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;">{{textSearch}}</div></td>
                    </tr>
                    <tr><td>Директория</td>
                        <td><div style="font-weight: bold; color: green; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;">{{pathSearch}}</div></td>
                    </tr>
                </table>
            </div>

            <div v-if="errorMessage" >
                <div style="font-weight: bold; color: red; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;" >{{errorMessage}}</div>
            </div>

        </v-row>
    </v-container></template>
    <!---- / SUB HEADER ----------->
    <hr>

    <!------ MAIN CONTENT --------->
    <v-app><v-main><v-container>

        <div class="main" >

            <div class="left-panel">

              <div v-for="(item, name) in dirList" >
                 <tree-view
                         :name="name"
                         :item="item">
                 </tree-view>
              </div>
            </div>

            <div class="content-panel">
                <template><div><v-tabs
                        v-model="tabActive" color="cyan" dark slider-color="yellow" >

                        <!------ Результаты поиска ------>
                        <v-tab>Результаты поиска</v-tab>
                        <v-tab-item ><v-card flat><v-card-text>
                            <div v-for="(item, path) in resultList">
                                <div @click="getFileContent(path)"
                                     style="font-weight: bold; padding:4px; border:1px gainsboro solid; cursor:pointer">{{path}}</div>
                                    <result-file-view style="margin-left:18px;"
                                          :list="item"
                                          :path="path"
                                    ></result-file-view>
                            </div>
                        </v-card-text></v-card></v-tab-item>

                        <!------ Содержимое файла ------>
                        <v-tab>Содержимое файла</v-tab>
                        <v-tab-item ><v-card flat><v-card-text>
                            <!--   <pre>{{fileContentList}}</pre>-->
                            <div v-for="(value) in fileContentList">
                                <pre>{{value}}</pre>
                            </div>
                        </v-card-text></v-card></v-tab-item>

                </v-tabs></div></template>
            </div>

        </div>

    </v-container></v-main></v-app>
    <!------ / MAIN CONTENT -------->
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
<script src="app.js"></script>

</body>
</html>
