<template>
    <div class="main">
        <div class="main__welcome">
            <form @submit.prevent>
                <div class="input">
                    <input type="text" class="input__control input__control--large" v-model="newColumn.title"
                           @keyup.enter="addColumn"
                           placeholder="New Column Title">

                    <div class="input__help">
                        Use joystick: <input type="checkbox" v-model="joystick" />
                    </div>
                </div>
            </form>

            <p v-cloak v-show="columns.length === 0" class="alert alert--warning">
                No <strong>Column</strong> available.
            </p>
        </div>

        <draggable tag="div" class="columns" v-model="columns" group="columns" @change="moveColumnDraggable">
            <div class="column column_max" v-for="(column, index) in columns" :key="column.id">
                <div class="column__title">
                    <h3>{{ column.title }}</h3>

                    <div v-show="joystick" class="column__buttons">
                        <span class="btn btn-sm btn-light" v-show="index !== 0"
                              @click="moveColumn(column, 'left')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 8 8 12 12 16"></polyline>
                                <line x1="16" y1="12" x2="8" y2="12"></line>
                            </svg>
                        </span>

                        <span class="btn btn-sm btn-light" v-show="index !== lastIndex"
                              @click="moveColumn(column, 'right')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 16 16 12 12 8"></polyline>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </span>
                    </div>

                    <div class="column__buttons">
                        <span @click="addCard(column)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="lightgreen" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line
                                x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </span>
                        <span @click="deleteColumn(column)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="#ff4040" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line
                                x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </span>
                    </div>
                </div>

                <small v-show="column.cards.length === 0" class="alert alert--warning alert--small">
                    No cards available.
                </small>

                <draggable tag="div" class="cards" v-model="column.cards" group="cards"
                           @change="moveCardDraggable(column, $event)">
                    <div v-for="(card, cardIndex) in column.cards" :key="card.id"
                         class="card" @click="showCard(card)">
                        <div class="card__title">
                            <h4>{{ card.title }}</h4>

                            <div class="card__buttons">
                                <div class="movement">
                                    <div class="movement__direction movement__direction--up"
                                         v-show="joystick && cardIndex !== 0"
                                         @click.stop="moveCard(card, 'up')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="#465A5A" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="18 15 12 9 6 15"></polyline>
                                        </svg>
                                    </div>
                                    <div class="movement__direction movement__direction--left"
                                         v-show="joystick && index !== 0"
                                         @click.stop="moveCard(card, 'left')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="#465A5A" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </div>
                                    <div class="movement__direction"
                                         :class="[joystick ? 'movement__direction--center' : 'movement__direction--topright']"
                                         @click.stop="deleteCard(column, card)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="#ff4040" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                        </svg>
                                    </div>
                                    <div class="movement__direction movement__direction--right"
                                         v-show="joystick && index !== lastIndex"
                                         @click.stop="moveCard(card, 'right')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="#465A5A" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </div>
                                    <div class="movement__direction movement__direction--down"
                                         v-show="joystick && cardIndex !== column.cards.length - 1"
                                         @click.stop="moveCard(card, 'down')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                             viewBox="0 0 24 24" fill="none" stroke="#465A5A" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="card__description">{{ card.description }}</p>
                    </div>
                </draggable>
            </div>
        </draggable>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from "vuedraggable";

import AddCard from './AddCardComponent';
import UpdateCard from './UpdateCardComponent';

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
            joystick: JSON.parse(localStorage.getItem('joystick') || "true")
        }
    },

    watch: {
        joystick(value) {
            localStorage.setItem('joystick', JSON.stringify(value))
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

                axios.post('/columns/' + e.added.element.column_id + '/cards/' + e.added.element.id + '/set', {
                    order: newOrder,
                    column: column.id
                })
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
                AddCard, {column}, {width: 360}, {'added': this.cardAdded}
            );
        },

        cardAdded(column, card) {
            let index = this.columns.indexOf(column);
            this.columns[index].cards.push(card)
        },

        showCard(card) {
            this.$modal.show(
                UpdateCard, {_card: card}, {width: 360}, {}
            );
        }
    }
}
</script>
