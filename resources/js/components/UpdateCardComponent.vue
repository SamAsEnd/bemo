<template>
    <form class="m-4">
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" type="text" class="form-control" v-model="card.title">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" class="form-control" v-model="card.description"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-outline-success" @click.prevent="updateCard">
                &circlearrowleft; Update card
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
