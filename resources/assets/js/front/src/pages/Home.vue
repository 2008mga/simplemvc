<template>
    <div id="home">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <datepicker
                                    language="ru"
                                    :format="customFormat"
                                    v-model="startDatetime"
                                    placeholder="Начальная дата"
                                    bootstrapStyling
                            >
                            </datepicker>
                        </div>
                        <div class="col-12 col-md-6">
                            <datepicker
                                    language="ru"
                                    :format="customFormat"
                                    v-model="endDatetime"
                                    placeholder="Конечная дата"
                                    bootstrapStyling
                            >
                            </datepicker>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12">
                    <div class="text-center my-3">
                        <ul class="list-inline by">
                            <li @click="by = 'hour'"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'hour'
                                }">
                                Час
                            </li>
                            <li @click="by = 'day'"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'day'
                                }">
                                День
                            </li>
                            <li @click="by = 'month'"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'month'
                                }">
                                Месяц
                            </li>
                            <li @click="by = 'year'"
                                :class="{
                                    'list-inline-item': true,
                                    'active': by === 'year'
                                }">
                                Год
                            </li>
                        </ul>
                    </div>

                    <p v-if="startDatetime && endDatetime" class="text-center">
                        Выбранно за период
                        <br />
                        {{ customFormat(startDatetime) }} - {{ customFormat(endDatetime) }}
                    </p>

                    <button
                            class="btn btn-primary mx-auto d-block btn-sm"
                            @click="Filter"
                    >Фильтровать</button>
                </div>
            </div>

            <hr>

            <line-example
                    width="500px"
                    height="200vh"
                    :labels="labels"
                    :datasets="datasets"
            ></line-example>
        </div>
    </div>
</template>

<script>
  import Datepicker from 'vuejs-datepicker';
  import moment from 'moment';
  import api from '@/api';
  import LineExample from '@/components/LineChart.js'

  export default {
    name: 'home',
    data() {
      return {
        endDatetime: null,
        startDatetime: null,
        by: 'year',
        labels: null,
        datasets: []
      }
    },
    components: {
      Datepicker,
      LineExample
    },
    methods: {
      customFormat(date) {
        return moment(date).format('YYYY-MM-DD');
      },
      Filter() {
        api.get('/by/' + this.by, {
          params: {
            'start_date': this.customFormat(this.startDatetime),
            'end_date': this.customFormat(this.endDatetime)
          }
        })
          .then((req) => {
            this.datasets = [];
            this.labels = req.data[0]['labels'];


            req.data.forEach((meta, index) => {
              this.datasets.push({
                'label': meta.label,
                'data': meta.data
              });
              console.log(meta, index);
            });
//            for (let meta, data in req.data) {

//            }
          });
      }
    },
    mounted() {

    }
  }
</script>

<style lang="sass">
    .by
        display: inline
        li
            background-color: #dedede
            padding: 5px 10px !important
            border-radius: 5px
            font-size: 12px
            cursor: pointer
        li.active
            background-color: #aeaeae
</style>