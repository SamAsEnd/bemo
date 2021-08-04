<template>
    <div class="container-fluid">
        <div class="row justify-content-center mb-4">
            <form class="form-inline">
                <div class="input-group">
                    <input type="text" class="form-control" v-model="newColumn.title">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" @click.prevent="addColumn">Add a Column</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-deck">
            <div class="card" v-for="column in columns" :key="column.id">
                <div class="card-header">
                    {{ column.title }}

                    <span class="float-right btn btn-sm btn-danger" @click="deleteColumn(column)">&times;</span>
                </div>

                <div class="card-body">
                    <div class="list-group">
                        <p v-show="column.cards.length === 0" class="alert alert-warning">
                            No cards available.
                        </p>

                        <div v-for="card in column.cards" :key="card.id" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ card.title }}</h5>
                                <span class="float-right btn btn-sm btn-outline-danger" @click="deleteCard(column, card)">&times;</span>
                            </div>

                            <p class="mb-1">{{ card.description }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-sm btn-info" @click="addCard(column)">Add Card</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import AddCard from './AddCardComponent';

export default {
    data() {
        return {
            columns: [],
            newColumn: {
                title: '',
            },
        }
    },

    mounted() {
        this.fetchData();
    },

    methods: {
        fetchData() {
            axios.get('/columns')
                .then((res) => {
                    this.columns = res.data
                })
        },

        addColumn() {
            axios.post('/columns', {
                title: this.newColumn.title,
            })
                .then((res) => {
                    this.columns.push(res.data)
                    this.newColumn.title = '';
                })
        },

        deleteColumn(column) {
            axios.delete('/columns/' + column.id)
                .then((res) => {
                    this.columns.splice(this.columns.indexOf(column), 1)
                })
        },

        deleteCard(column, card) {
            axios.delete('/columns/' + column.id + '/cards/' + card.id)
                .then((res) => {
                    let index = this.columns.indexOf(column);
                    let cards = this.columns[index].cards;

                    cards.splice(cards.indexOf(card), 1)
                })
        },

        addCard(column) {
            this.$modal.show(
                AddCard, {column}, {}, {'added':this.cardAdded}
            );
        },

        cardAdded(column, card) {
            let index = this.columns.indexOf(column);
            this.columns[index].cards.push(card)
        }
    }
}
</script>
