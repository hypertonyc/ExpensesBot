<template>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header"><h3>Категории расходов</h3></div>
                  <div class="card-body">
                    <div class="section-block">
                      <h4>Добавить новую категорию</h4>
                      <form method="post" enctype="multipart/form-data" v-on:submit.prevent="addCategory()">
                        <div class="form-group">
                          <label for="catname" class="control-label">Наименование</label>
                          <input class="form-control" type="text" placeholder="Категория" id="catname" v-model="newCategoryName">
                        </div>
                        <button type="submit" class="btn btn-success">Добавить категорию</button>
                      </form>
                    </div>
                    <div class="section-block">
                      <h4>Список категорий</h4>
                      <p v-if="categories.length == 0">Нет ни одной категории</p>
                      <ul v-else class="list-group list-group-flush">
                        <li v-for="category of categories" class="list-group-item">{{category.name}}</li>
                      </ul>
                    </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>  
  export default {

    data() {
      return {
        newCategoryName: '',
        categories: []
      }
    },

    methods: {
      getCategories() {
        axios.get('/api/categories')
        .then(response => {
          this.categories = response.data.categories;
        })
        .catch(e => {
           toastr.error(e, 'Произошла ошибка', {timeout:5000});
        })
      },
      addCategory() {
        axios.get('/api/categories/create', {
          params:{
            name: this.newCategoryName,
            position: this.categories.length
          }
        })
        .then(response => {
          this.categories.push({
            id: response.data.id,
            name: this.newCategoryName,
            position: this.categories.length
          });
          toastr.success('Категория "' + this.newCategoryName + '" успешно добавлена', 'Добавлена новая категория', {timeout:5000});
          this.newCategoryName = '';
        })
        .catch(e => {
          toastr.error(e, 'Произошла ошибка', {timeout:5000});
        })
      }
    },

    mounted() {
        this.getCategories();
    }
  }
</script>
