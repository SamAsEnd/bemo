<template>
    <form class="modal">
        <div class="input">
            <label for="title" class="input__label">Title</label>
            <input id="title" type="text" class="input__control" v-model="card.title">
        </div>

        <div class="input">
            <label for="description" class="input__label">Description</label>
            <textarea id="description" class="input__control" v-model="card.description"></textarea>
        </div>

        <div class="input">
            <button class="button" @click.prevent="addCard">
                Add card
            </button>
        </div>
    </form>
</template>

<script>
import axios from 'axios';

export default {
    props: ['column'],

    data() {
        return {
            card: {
                title: '',
                description: '',
                column_id: this.column.id,
            },
        }
    },

    methods: {
        addCard() {
            axios.post('/columns/' + this.card.column_id + '/cards', this.card)
                .then((res) => {
                    this.$parent.$emit('added', this.column, res.data);
                    this.$emit('close');
                })
        },
    }
}
</script>
