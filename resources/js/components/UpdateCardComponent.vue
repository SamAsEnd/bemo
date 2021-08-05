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
            <button class="button" @click.prevent="updateCard">
                Update card
            </button>
        </div>
    </form>
</template>

<script>
import axios from 'axios';

export default {
    props: ['_card'],

    data() {
        return {
            card: {...this._card},
        }
    },

    methods: {
        updateCard() {
            axios.patch('/columns/' + this.card.column_id + '/cards/' + this.card.id, this.card)
                .then((res) => {
                    this._card.title = res.data.title;
                    this._card.description = res.data.description;
                    this.$emit('close');
                })
        },
    }
}
</script>
