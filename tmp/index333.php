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

    <link rel="stylesheet" href="assets/base.css">
    <link href="assets/style.css" rel="stylesheet">
<!--    <link rel="stylesheet" href="assets/prettify.css">-->

    <style>
        .container {
            max-width: 90% !important;
        }

        .v-btn {
            background: royalblue !important;
            color:white !important;
            margin:3px;
        }

    </style>

</head>
<body>

<div id="app" >

    <nav>
        <div class="container">
            <h1 style="font-size: 17px; font-style: italic" >Поисковый сервис</h1>
            <div id="menu">
<!--                <ul class="toplinks">-->
<!--                    <li><a href="http://www.greepit.com/Opineo/admin-form.html">Opineo Website </a></li>-->
<!--                    <li><a href="http://www.egrappler.com/">eGrappler</a></li>-->
<!--                    <li><a href="../doc-template-red/docs.html">Red Theme</a></li>-->
<!--                    <li><a href="../doc-template-green/docs.html">Green Theme</a></li>-->
<!--                </ul>-->
            </div>
            <a id="menu-toggle" href="#" class=" ">&#9776;</a>
        </div>
    </nav>

    <header style="padding:0px; margin: 0px;" >
        <div class="container" >
            <template  >
                <div v-if="preloader" class="text-center" >
                    <v-progress-circular
                            indeterminate
                            color="primary"
                    ></v-progress-circular>
                </div>
            </template>
        </div>
    </header>

    <!--------------------------------
    <section>
        <div class="container">

            <ul class="docs-nav" id="menu-left">

                <li><strong>Getting Started</strong></li>
                <li><a href="#welcome" class=" ">Welcome</a></li>
                <li><a href="#benefits" class=" ">Benefits</a></li>

                <li class="separator"></li>

                <li><strong>Customizing Opineo</strong></li>
                <li><a href="#view_type" class=" ">View Type</a></li>
                <li><a href="#animation_style" class=" ">Animation Styles</a></li>
                <li><a href="#bars_text" class=" ">Bars Text</a></li>

            </ul>

            <div class="docs-content">

                <h2> Getting Started</h2>
                <h3 id="welcome"> Welcome</h3>
                <p> Are you listening to your customers?</p>

                <p> As they say: You cannot improve what you cannot measure; but the paradox is you
                    cannot measure everything – happiness, hatred, anger… but you can measure customer
                    satisfaction. Yes, you can measure customer satisfaction by analyzing likes and
                    dislikes of your customers. You can gauge popularity of your website or products.
                    You can also:
                </p>
                <ul>
                    <li>See how many visitors like the new design of your website or logo</li>
                    <li>Analyze what your readers want to see on your blog</li>
                    <li>Understand how helpful the content on your support forum or website is</li>
                    <li>Know the latest trends and user’s opinion before launching a new product or service</li>
                </ul>

                <h3 id="features"> Features</h3>
                <ul>
                    <li>Facility to customize to match your website theme</li>
                    <li>Detailed and Compact view options</li>
                    <li>Comprehensive options to customize animation, colors, orientation and style</li>
                    <li>All the power and flexibility of jQuery</li>
                    <li>Easy install; 100% integration</li>
                    <li>Facility to customize rating icons</li>
                </ul>

                <p> The following customization options are available in Opineo:</p>

                <h3 id="view_type"> View Type</h3>
                <ul><li>Detailed View</li></ul>

                <pre class="prettyprint">
                    &lt;script&gt;
                    $(document).ready(function (){
                    $('#DefaultOptions').opineo('results.php', {curvalue:3,
                        view: 'detailed',
                        animation_speed: 'super',
                        show_total_votes_counter: false,
                        show_overall_rating: true});
                     })
                     &lt;/script&gt;
            </pre>

                <ul><li>Text For Fourth Star</li></ul>
                <pre class="prettyprint">&lt;script&gt;
                $(document).ready(function (){
                    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_1_text:'Like It'});
                })
                &lt;/script&gt;
            </pre>

                <h3 id="bar_colors"> Bar Colors</h3>
                <ul>
                    <li>Colors of Red Bar</li>
                    <li>Colors of Yellow Bar</li>
                    <li>Colors of Green Bar</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="vibrant centered">
        <div class="container">
            <h4> This documentation template is provided free by eGrappler.com. Opineo is a feedback
                collection widget and is available for free download
            </h4>
        </div>
    </section>

    <footer>
        <div class="container">
            <p> &copy; 2014 EGrappler.com </p>
        </div>
    </footer>
    --------------------------->

    <!---- SUB HEADER       -------->
    <v-container>
        <v-row align="center" justify="space-around" style="padding:0px !important; margin:0px !important;" >

            <v-col cols="12" sm="12" md="12" >
                <v-text-field v-model="apiUrl" label="Путь до поискового файла" style="color:red; background: ghostwhite; border:gainsboro 1px solid" ></v-text-field>
            </v-col>

            <v-btn @click="setDir('apache')" >Директория Apache</v-btn>
            <v-btn @click="setDir('system')" color="green">Директория Системы</v-btn>
            <v-btn @click="clearResult()" color="green">Очистить результаты</v-btn>

            <v-btn @click="updateElemCss('.container', {'width' : '30%'})" color="green" > cont 30</v-btn>
            <v-btn @click="updateElemCss('.container', {'width' : '100%'})" color="green">cont 100</v-btn>
            <v-btn @click="updateElemCss('.container', {'width' : '50%'})" color="green" >cont 50</v-btn>


<!--            <div style="margin-top:-10px; margin-left:20px;" >-->
<!--                <table>-->
<!--                    <tr><td>Строка</td><td><div style="font-weight: bold; color: green; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;">{{textSearch}}</div></td></tr>-->
<!--                    <tr><td>Директория</td><td><div style="font-weight: bold; color: green; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;">{{pathSearch}}</div></td></tr>-->
<!--                </table>-->
<!--            </div>-->

<!--            <div v-if="errorMessage" >-->
<!--                <div style="font-weight: bold; color: red; margin:5px; padding:5px; border:1px gainsboro solid; font-size:18px;" >{{errorMessage}}</div>-->
<!--            </div>-->

        </v-row>
    </v-container>
    <!---- / SUB HEADER  ----------->


    <!------ MAIN CONTENT  --------->
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


                <!------ SEARCH CONTAINER ----->
                <div class="search-form-container" >

                    <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid" >
                        <v-col cols="12" sm="12" md="12" >
                            <v-text-field v-model="textSearch" label="Строка поиска" style="color:red; background: ghostwhite; border:gainsboro 1px solid" ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="12" md="12" >
                            <v-text-field v-model="pathSearch" label="Директория поиска" style="color:red; background: ghostwhite; border:gainsboro 1px solid" ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row style="margin:0px !important; padding:0px !important; border:1px gainsboro solid" >
                        <v-col cols="12" sm="12" md="12" >
                            <div style="display: flex;" >
                                <v-btn @click="findTextToFiles()" style="background: green !important; color:white;margin:3px; width:50%;" >Поиск по содержимому в файлах</v-btn>
                                <v-btn style="background: orange; color:white; margin:3px; width:50%;" >Поиск по имени файла</v-btn>
                            </div>

                            <div style="display: flex;" >
                                <v-btn style="background: lightseagreen; color:white;margin:3px; width:50%;" >Поиск по имени папки</v-btn>
                                <v-btn style="background: royalblue; color:white;margin:3px; width:50%;" @click="catalogUpdate()" >Обновить каталог</v-btn>
                            </div>
                        </v-col>
                    </v-row>

                </div>
                <!---- / SEARCH CONTAINER ------->


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
