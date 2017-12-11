<template>
    <div id="home">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2 mt-4">
                    <div class="row">
                        <div class="col-12 col-md-12 text-center">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <label for="">Дата</label>
                                    <datepicker
                                            language="ru"
                                            :format="customFormat"
                                            v-model="date"
                                            :placholder="date"
                                            @input="Filter"
                                            bootstrapStyling
                                    >
                                    </datepicker>
                                </div>
                                <div v-if="by === 'hour'">
                                    <label for="" class="d-block">Время</label>
                                    <vue-timepicker
                                            v-model="times"
                                            format="HH"
                                            @change="Filter"
                                    ></vue-timepicker>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12">
                    <div class="text-center my-3">
                        <ul class="list-inline by">
                            <li @click="by = 'hour'; Filter()"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'hour'
                                }">
                                Час
                            </li>
                            <li @click="by = 'day'; Filter()"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'day'
                                }">
                                День
                            </li>
                            <li @click="by = 'month'; Filter()"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'month'
                                }">
                                Месяц
                            </li>
                            <li @click="by = 'year'; Filter()"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'year'
                                }">
                                Год
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr>

            <line-example
                    :labels="labels"
                    :datasets="datasets"
                    :height="200"
            ></line-example>
        </div>
    </div>
</template>

<script>
  import Datepicker from 'vuejs-datepicker';
  import moment from 'moment';
  import api from '@/api';
  import LineExample from '@/components/LineChart.js'
  import VueTimepicker from 'vue2-timepicker'

  export default {
    name: 'home',
    data() {
      return {
        date: this.customFormat(moment.now()),
        by: 'year',
        labels: null,
        datasets: [],
        times: {
          'HH': '00'
        }
      }
    },
    components: {
      Datepicker,
      LineExample,
      VueTimepicker
    },
    methods: {
      customFormat(date) {
        return moment(date).format('YYYY-MM-DD');
      },
      Filter() {
        api.get('/by/' + this.by, {
          params: {
            'date': this.customFormat(this.date) + ' ' + this.times.HH + ':00:00'
          }
        })
          .then((req) => {
            this.datasets = req.data.datasets;
            this.labels = req.data['labels'];

          });
      }
    },
    mounted() {
        this.Filter();
    }
  }
</script>

<style lang="sass">
    .by
        display: inline
        li
            background-color: #34495e
            color: white
            padding: 5px 10px !important
            border-radius: 5px
            font-size: 12px
            cursor: pointer
        li.active
            background-color: lighten(#34495e, 10%)
    .time-picker
      input
        background-color: #34495e
        border-color: darken(#34495e, 10%) !important
        color: white
        padding: 6px 10px !important
        height: auto !important
      .clear-btn
        display: none !important
</style>
