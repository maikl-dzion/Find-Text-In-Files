
const EventGlobalBus = new Vue();

Vue.mixin({

    data() {
        return {
            apiUrl : ApiUrl,
        }
    },

    methods : {

        createEvent(eventName, data = {}) {
            EventGlobalBus.$emit(eventName, data );
        },

        getEvent(eventName, fn) {
            EventGlobalBus.$on(eventName, (response) => {
                fn(response)
            });
        },

        async get(url, fn) {
            const uri = this.apiUrl + url;
            try {
                this.createEvent('start-time', { start : 1})
                const response = await axios.get(uri);
                let result = this.responseHandler(response, fn);
                this.createEvent('end-time', { end : 1})
                if(result)
                    return result;
                console.log(response);
            } catch (error) {
                lg(['HTTP ERROR - GET', error]);
                console.error(error);
            }

        },

        async post(url, data = null, fn = null) {
            const uri = this.apiUrl + url;
            try {
                this.createEvent('start-time', { start : 1})
                const response = await axios.post(uri, data);
                let result = this.responseHandler(response, fn);
                this.createEvent('end-time', { end : 1})
                if(result)
                    return result;
            } catch (error) {
                lg(['HTTP ERROR - POST', error]);
                console.error(error);
            }
        },

        responseHandler(response, fn = null) {

            let httpInfo = this.setHttpInfo(response);

            if(typeof response.data != 'object') {
                this.createEvent('http-info', httpInfo);
                lg(['HTTP ERROR', response.data]);
                return false;
            }

            const data = response.data;

            if(!this.isEmpty(data.error)) {
                this.createEvent('response-error', data.error);
                return false;
            }

            httpInfo['warn'] = (!this.isEmpty(data.warn)) ? data.warn : '';
            httpInfo['info'] = (!this.isEmpty(data.info)) ? data.info : '';
            this.createEvent('http-info', httpInfo);
            this.createEvent('response-data', data);

            if(fn) {
                fn(data);
                return false;
            }

            return data;
        },

        setHttpInfo(response) {
            let httpInfo = {};
            httpInfo.code    = response.status;
            httpInfo.text    = response.statusText;
            httpInfo.uri     = response.config.url;
            httpInfo.headers = response.headers;
            return httpInfo;
        },

        // Получить содержимое файла
        getFileContent(path, type = 'arr') {
            const url = '/file/content/get';
            const data = { path, type };
            this.post(url, data, (response) => {
                this.createEvent('file-content', { file : response.result })
            });
        },

        updateElemCss(selector, json) {
            $(selector).css(json);
        },

        isEmpty(obj) {
            for (var i in obj) {
                if (obj.hasOwnProperty(i)) {
                    return false;
                }
            }
            return true;
        },

        startSetInterval() {
            let date = new Date();
            this.timerInfo.start = date;
            this.setTimerId = setInterval(() => {
                let date = new Date();
                this.timerDate = date;
                this.timerInfo.end = date;
            }, 5);
        },

        stopSetInterval() {
            let date = new Date();
            // this.timerInfo.end = date.toLocaleTimeString();
            this.timerInfo.end = date;
            clearInterval(this.setTimerId);
        },

        testAction() {
            const url = '/test/service/router';
            const data = { service : 'router' };
            this.post(url, data, (response) => {
                 lg(response);
            });
        },

    },

    mounted() {

        $(document).ready(function() {
            $(".tab-panel-button").click(function() {
                $(".tab-panel-button").css({'border-bottom' : ''});
                $(this).css({'border-bottom' : '2px solid #ffeb3b'});
            });
        });

    },

})


new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    data: {

        rootPath : '',
        preloader: false,
        panelActive : 'find-result',
        errorMessage: '',

        pathSearch: 'C:/OpenServer/domains/localhost/FindText/test/src', // директория поиска
        textSearch: 'test_id_2345666TYkyi', // по этому тексту ищем

        dirList     : {},

        resultList: [],
        fileContentList: [],

        httpInfo : {},
        respData  : {},
        respError : {},

        setTimerId : 0,
        timerDate  : '',
        timerInfo  : {
            start : '',
            end   : ''
        },

    },

    created() {

        this.getServerDir();

        // this.startSetInterval();

        // this.getStore();
    },

    // --- METHODS
    methods: {

        // -- Устанавливаем директории web сервера
        getServerDir() {
            let name = 'server';
            this.getDirPath(name);
            this.get('/scan/dir/' + name, response => {
                this.dirList = response['result'];
            });
        },

        // -- Устанавливаем директории OS
        getSystemDir() {
            let name = 'system';
            this.getDirPath(name);
            this.get('/scan/dir/' + name, response => {
                this.dirList = response['result'];
            });
        },

        getDirPath(name) {
            this.get('/get/dir/path/' + name, response => {
                this.rootPath = response['result'];
                this.pathSearch = this.rootPath;
            });
        },

        // -- Поиск по файлам
        findTextToFiles() {

            this.resultList = [];
            this.errorMessage = '';

            let text = this.textSearch;
            let path  = this.pathSearch;
            if (!text) {
                alert('Поле для поиска пустое');
                return;
            }

            const url = '/find/text';
            this.preloader = true;
            this.post(url, { path, text }, response => {
                this.preloader = false;
                if (response['error'])
                    this.errorMessage = response['error'];

                this.resultList = response.result;

                this.panelActive = 'find-result';

                // this.setStore('text_value', value);
                // this.setStore('path_name', path);
                // this.setStore('text_value', value);
                // this.setStore('api_url'   , this.apiUrl);

            });
        },


        catalogUpdate() {
            this.setStore('api_url', this.apiUrl);
            this.setDir('apache');
        },

        clearResult() {
            this.resultList = [];
            this.fileContentList = [];
        },

        setStore(name, value) {
            localStorage.setItem(name, value);
        },

        getStore() {
            if (!this.textSearch)
                this.textSearch = localStorage.getItem('text_value');

            if (!this.pathSearch)
                this.pathSearch = localStorage.getItem('path_name');

            let apiUrl = localStorage.getItem('api_url');
            if (apiUrl)
                this.apiUrl = apiUrl;

        },

    },  // --- / METHODS

    mounted() {

        this.getEvent('set-path', (resp) => {
            this.pathSearch = resp.path;
        });

        this.getEvent('file-content', (resp) => {
            this.fileContentList = resp.file;
            this.panelActive = 'file-content';
        });

        this.getEvent('http-info', (info) => {
            this.httpInfo = info;
        });

        this.getEvent('response-error', (error) => {
            this.respError = error;
            lg(['Сервер сгенерировал ошибку', this.respError]);
        });

        this.getEvent('response-data', (data) => {
            this.respData = data;
        });

        this.getEvent('start-time', (data) => {
            this.startSetInterval()
        });

        this.getEvent('end-time', (data) => {
            this.stopSetInterval();
        });

    },

});
