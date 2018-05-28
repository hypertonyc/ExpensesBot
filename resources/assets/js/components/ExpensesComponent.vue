<template>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header"><h3>Расходы</h3></div>

                  <div class="card-body">
                    <div class="section-block">
                      <h4>Список категорий</h4>
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
        expenses: []
      }
    },

    methods: {
      getExpenses() {
        axios.get('/api/expenses')
        .then(response => {
          this.expenses = response.data.expenses;
        })
        .catch(e => {
           toastr.error(e, 'Произошла ошибка', {timeout:5000});
        })
      }
    },

    mounted() {
        this.getExpenses();
    }
  }
</script>
