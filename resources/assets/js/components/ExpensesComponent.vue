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
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == '') }" v-on:click.prevent="setToday()">Сегодня</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'week') }" v-on:click.prevent="setWeek()">С начала недели</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'month') }" v-on:click.prevent="setMonth()">С начала месяца</a>
                        <a class="nav-link" href="#" v-bind:class="{ active: (period == 'month') }" v-on:click.prevent="setPeriod()">За период</a>
                      </nav>
                      <p v-if="expenses.length == 0">Нет ни одного расхода</p>

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
</template>

<script>
  import axios from 'axios';
  export default {

    data() {
      return {
        period: '',
        expenses: []
      }
    },

    methods: {
      getExpenses() {
        axios.get('/api/expenses/' + this.period)
        .then(response => {
          this.expenses = response.data.expenses;
        })
        .catch(e => {
           toastr.error(e, 'Произошла ошибка', {timeout:5000});
        })
      },
      setToday() {
        this.period = '';
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
        this.period = 'month';
        this.getExpenses();
      }
    },

    computed : {
      total: function() {
        let sum = 0;
        return this.expenses.reduce((sum, item) => sum + item.amount, 0);
      }
    },

    mounted() {
        this.getExpenses();
    }
  }
</script>
