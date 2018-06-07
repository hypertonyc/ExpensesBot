<template>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header"><h3>Расходы</h3></div>

                  <div class="card-body">
                    <div class="section-block">
                      <h4>Таблица расходов</h4>
                      <nav class="nav">
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'today') }" v-on:click.prevent="setToday()">Сегодня</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'week') }" v-on:click.prevent="setWeek()">С начала недели</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'month') }" v-on:click.prevent="setMonth()">С начала месяца</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'period') }" v-on:click.prevent="setPeriod()">За период</a>
                      </nav>
                      <div class="section-block">
                        <div class="container" v-show="period == 'period'">
                          <div class="row">
                            <div class="form-group col-sm-4">
                              <label>С:</label>
                              <date-picker v-model="periodFrom" :config="dtOptions"></date-picker>
                            </div>
                            <div class="form-group col-sm-4">
                              <label>По:</label>
                              <date-picker v-model="periodTo" :config="dtOptions"></date-picker>
                            </div>
                          </div>
                        </div>
                      </div>
                      <p v-if="loading" class="text-center">Загрузка...</p>
                      <div v-else class="section-block">
                        <p v-if="expenses.length == 0"  class="text-center">Нет ни одного расхода</p>
                        <table v-else class="table table-striped">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Дата и время</th>
                              <th scope="col">Категория</th>
                              <th scope="col">Сумма</th>
                              <th scope="col">Комментарий</th>
                              <th scope="col">Пользователь</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(expense, index) in expenses">
                              <td>{{index + 1}}</td>
                              <td>{{expense.created_at}}</td>
                              <td>{{expense.category.name}}</td>
                              <td>{{expense.amount}}</td>
                              <td>{{expense.description}}</td>
                              <td>{{expense.user.name}}</td>
                            </tr>
                          </tbody>
                        </table>
                        <p class="total">Итого: {{total}}</p>
                      </div>
                    </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
  import datePicker from 'vue-bootstrap-datetimepicker';
  import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

  export default {

    data() {
      return {
        loading: true,
        myDoughnutChart: null,
        period: 'today',
        periodFrom: new Date(),
        periodTo: new Date(),
        dtOptions: {locale: 'ru'},
        expenses: []
      }
    },

    methods: {
      getExpenses() {
        var _self = this;

        this.loading = true;

        var expensesUrl = '/api/expenses/' + this.period;
        if (this.period == 'period') {
          expensesUrl += '/' + moment(this.periodFrom).unix() + '/' + moment(this.periodTo).unix();
        }

        axios.get(expensesUrl)
        .then(response => {
          _self.expenses = response.data.expenses;
        })
        .catch(e => {
          toastr.error(e, 'Произошла ошибка', {timeout:5000});
        })
        .then(() => {
          _self.loading = false;
        });
      },
      setToday() {
        this.period = 'today';
        this.getExpenses();
      },
      setWeek() {
        this.period = 'week';
        this.getExpenses();
      },
      setMonth() {
        this.period = 'month';
        this.getExpenses();
      },
      setPeriod() {
        this.period = 'period';
        this.getExpenses();
      }
    },

    computed: {
      total: function() {
        var tmp = 0;
        var sum = this.expenses.reduce(function(sum, item) {
          return tmp + item.amount;
        }, 0);
        return sum.toFixed(2);
      }
    },

    components: {
      datePicker
    },

    mounted() {
      this.periodFrom.setHours(0,0,0,0);
      this.periodTo.setHours(23,59,59,999);

      this.getExpenses();
    }
  }
</script>
