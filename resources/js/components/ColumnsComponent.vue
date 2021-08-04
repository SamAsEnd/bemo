<template>
    <div class="container-fluid">
        <div class="row justify-content-center mb-4">
            <form @submit.prevent class="form-inline">
                <div class="input-group">
                    <input type="text" class="form-control" v-model="newColumn.title">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button"
                                @click.prevent="addColumn"
                                :disabled="newColumn.title.trim().length === 0">
                            &plus; Add a Column
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div v-show="columns.length === 0" class="row justify-content-center mb-4">
            <p class="alert alert-warning">
                No column available.
            </p>
        </div>

        <draggable tag="div" class="card-deck" v-model="columns" group="columns" @change="moveColumnDraggable">
            <div class="card column_max sortable-list" v-for="(column, index) in columns" :key="column.id">
                <div class="card-header">
                    {{ column.title }}

                    <div class="float-right">
                        <span class="btn btn-sm btn-light" v-show="index !== 0" @click="moveColumn(column, 'left')">&larr;</span>
                        <span class="btn btn-sm btn-light" v-show="index !== lastIndex"
                              @click="moveColumn(column, 'right')">&rarr;</span>
                        <span class="btn btn-sm btn-danger" @click="deleteColumn(column)">&times;</span>
                    </div>
                </div>

                <div class="card-body">
                    <p slot="header" v-show="column.cards.length === 0" class="alert alert-warning">
                        No cards available.
                    </p>

                    <draggable tag="div" class="list-group" v-model="column.cards" group="cards"
                               @change="moveCardDraggable(column, $event)">
                        <div v-for="(card, cardIndex) in column.cards" :key="card.id"
                             class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ card.title }}</h5>

                                <div class="float-right">
                                    <span class="btn btn-sm btn-light" v-show="index !== 0"
                                          @click="moveCard(card, 'left')">&larr;</span>
                                    <span class="btn btn-sm btn-light" v-show="index !== lastIndex"
                                          @click="moveCard(card, 'right')">&rarr;</span>

                                    <span class="btn btn-sm btn-light" v-show="cardIndex !== 0"
                                          @click="moveCard(card, 'up')">&uarr;</span>
                                    <span class="btn btn-sm btn-light" v-show="cardIndex !== column.cards.length - 1"
                                          @click="moveCard(card, 'down')">&darr;</span>

                                    <span class="btn btn-sm btn-outline-danger"
                                          @click="deleteCard(column, card)">&times;</span>
                                </div>
                            </div>

                            <p class="mb-1">{{ card.description }}</p>
                        </div>
                    </draggable>
                </div>

                <div class="card-footer">
                    <button class="btn btn-sm btn-info" @click="addCard(column)">&plus; Add Card</button>
                </div>
            </div>
        </draggable>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from "vuedraggable";

import AddCard from './AddCardComponent';

export default {
    components: {
        draggable
    },

    data() {
        return {
            columns: [],
            newColumn: {
                title: '',
            },
        }
    },

    computed: {
        lastIndex() {
            return this.columns.length - 1;
        },
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

        moveColumn(column, direction) {
            axios.post('/columns/' + column.id + '/move/' + direction)
                .then((res) => {
                    this.fetchData();
                })
        },

        moveCard(card, direction) {
            axios.post('/columns/' + card.column_id + '/cards/' + card.id + '/move/' + direction)
                .then((res) => {
                    this.fetchData();
                })
        },

        moveColumnDraggable({moved: {element, newIndex, oldIndex}}) {
            let newOrder = (() => {
                if (newIndex === 0) { //first
                    return this.columns[1].order - 10;
                }

                if (newIndex === this.lastIndex) { // last
                    return this.columns[this.lastIndex - 1].order + 10;
                }

                return (this.columns[newIndex - 1].order + this.columns[newIndex + 1].order) / 2.0;
            })();

            axios.post('/columns/' + element.id + '/set', {order: newOrder})
                .then((res) => {
                    this.fetchData();
                })
        },

        moveCardDraggable(column, e) {
            if (e.moved) {
                let newOrder = (() => {
                    if (e.moved.newIndex === 0) { //first
                        try {
                            return column.cards[1].order - 10;
                        } catch (e) {
                            return 10;
                        }
                    }

                    if (e.moved.newIndex === column.cards.length - 1) { // last
                        return column.cards[column.cards.length - 2].order + 10;
                    }

                    return (column.cards[e.moved.newIndex - 1].order + column.cards[e.moved.newIndex + 1].order) / 2.0;
                })();

                axios.post('/columns/' + column.id + '/cards/' + e.moved.element.id + '/set', {order: newOrder})
                    .then((res) => {
                        this.fetchData();
                    })
            } else if (e.added) {
                let newOrder = (() => {
                    if (e.added.newIndex === 0) { //first
                        try {
                            return column.cards[1].order - 10;
                        } catch (e) {
                            return 10;
                        }
                    }

                    if (e.added.newIndex === column.cards.length - 1) { // last
                        return column.cards[column.cards.length - 2].order + 10;
                    }

                    return (column.cards[e.added.newIndex - 1].order + column.cards[e.added.newIndex + 1].order) / 2.0;
                })();

                axios.post('/columns/' + e.added.element.column_id + '/cards/' + e.added.element.id + '/set', {order: newOrder, column: column.id})
                    .then((res) => {
                        this.fetchData();
                    })
            }
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
                AddCard, {column}, {}, {'added': this.cardAdded}
            );
        },

        cardAdded(column, card) {
            let index = this.columns.indexOf(column);
            this.columns[index].cards.push(card)
        }
    }
}
</script>
