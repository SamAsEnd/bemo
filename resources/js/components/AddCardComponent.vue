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
            <button class="btn btn-outline-primary" @click.prevent="addCard">
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

    mounted() {
        axios.get('/columns')
            .then((res) => {
                this.columns = res.data
            })
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
