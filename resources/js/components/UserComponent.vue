<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Users</h1>
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Salary</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <th scope="row">{{ user.id }}</th>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.salary }}</td>
                            <td class="action-button" @click="editUser(user)">
                                <i class="fa fa-pencil" aria-hidden="true" ></i>
                            </td>
                            <td class="action-button" @click="deleteUser(user.id)">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" @click="addUser()" class="btn btn-success">Add User</button>
            </div>
            <hr>
            <div v-if="this.users.length" class="col-md-12">
                <div class="chart-page">
                    <h3>Salaries chart</h3>
                    <bar-chart  :chart-data="chartData" :options="{
                        responsive: true,
                        scales: {
                            xAxes: [{
                                barPercentage: 0.4,
                                barThickness: 'flex',

                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: { callbacks: { title: function(tooltipItem, data) { return 'Salary'; }}},
                    }"/>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" id="user-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User modal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="errors" v-if="modal.errors.length">
                        <ul>
                            <li v-for="error in modal.errors" :key="error">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="email" class="form-control" id="name" v-model="modal.name" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" v-model="modal.email"  placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" class="form-control" id="salary" v-model="modal.salary"  placeholder="Salary">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" @click="saveUser(id=null)" class="btn btn-primary">Save user</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import BarChart from './ChartComponent.vue'
    export default {
        name: 'UserComponent',
        components: { BarChart },
        data: function () {
            return {
                users: [],
                chartData: null,
                modal: {
                    name: "",
                    id: null,
                    email: "",
                    salary: "",
                    errors: []
                }
            }
        },
        methods: {
            fillCharts: function(){
                const data = this.users;
                let datasets = [];
                let dataLabels = [];
                let dataValues = [];
                if(data.length){
                    for(var i=0 ; i<data.length; i++)
                    {
                        let dataset = {
                            backgroundColor: this.$parent.getRandomColor(),
                            label: data[i].name,
                            data: [data[i].salary]
                        }
                        datasets.push(dataset);
                    }

                }
                this.chartData = {
                    datasets
                }
            },
            deleteUser: function(id){
                axios.post('/api/user/delete', {"id": id})
                .then( (response) => {
                    if(response.status == 200){
                        for(var i=0 ; i<this.users.length; i++)
                        {
                            if(this.users[i].id==id){
                                this.users.splice(i, 1);
                                break;
                            }

                        }
                        this.fillCharts();
                    }else{
                        alert("Operation's failed! Please, try again!");
                    }
                })

            },
            clearModal: function(){
                this.modal.name = "";
                this.modal.email = "";
                this.modal.salary = "";
                this.modal.id = null;
                this.modal.errors = [];
            },
            addUser: function(){
                $('#user-modal').modal('show')
                this.clearModal();
            },
            editUser: function(user){
                $('#user-modal').modal('show')
                this.modal.name = user.name;
                this.modal.email = user.email;
                this.modal.salary = user.salary;
                this.modal.id = user.id;

            },
            saveUser: function(){
                const user = {
                    name: this.modal.name,
                    email: this.modal.email,
                    salary: this.modal.salary,
                }
                let action = 'add';
                if(this.modal.id){
                    user.id = this.modal.id;
                    action = 'edit';
                }
                this.modal.errors = [];
                axios.post('/api/user/'+action, user)
                .then( (response) => {
                    if(response.status == 200){
                        if(this.modal.id){
                            for(let item in this.users){
                                if(this.users[item].id == this.modal.id){
                                    this.users[item].name = this.modal.name;
                                    this.users[item].email = this.modal.email;
                                    this.users[item].salary = this.modal.salary;
                                }
                            }
                        }else{
                            const newUser = {
                               name: this.modal.name,
                               email: this.modal.email,
                               salary: this.modal.salary,
                               id: response.data.id
                            }
                            this.users.push(newUser);
                        }
                        this.fillCharts();
                        this.clearModal();
                        $('#user-modal').modal('hide')
                    }
                })
                .catch( (error, mess) => {
                    const errs = error.response.data.errors;
                     for(var item in  errs){
                        for(var i = 0; i < errs[item].length; i++){
                            this.modal.errors.push(errs[item][i]);
                        }
                    }
                });
            }
        },
        mounted() {
            axios.get(
                '/api/user/', {
                params: {
                }
            })
            .then( (response) => {
                if(response.status == 200){
                    this.users = response.data;
                    this.fillCharts();
                }
            })


        }
    }
</script>
