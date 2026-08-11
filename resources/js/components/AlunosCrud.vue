<template>
    <div class="container">
        <h1>CRUD de Alunos</h1>

        <div v-if="message" class="message" :class="message.type">
            {{ message.text }}
        </div>

        <form @submit.prevent="submitAluno">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input id="nome" v-model="alunoForm.nome" type="text">
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" v-model="alunoForm.email" type="email">
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    {{ editingId ? 'Salvar alterações' : 'Cadastrar' }}
                </button>
                <button v-if="editingId" type="button" class="btn btn-secondary" @click="resetForm">
                    Cancelar
                </button>
            </div>
        </form>

        <div v-if="validationErrors.length" class="error-list">
            <ul>
                <li v-for="(error, index) in validationErrors" :key="index">{{ error }}</li>
            </ul>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="aluno in alunos" :key="aluno.id">
                    <td>{{ aluno.nome }}</td>
                    <td>{{ aluno.email }}</td>
                    <td>
                        <button class="btn btn-small btn-edit" @click="editarAluno(aluno)">Editar</button>
                        <button class="btn btn-small btn-danger" @click="excluirAluno(aluno)">Excluir</button>
                    </td>
                </tr>
                <tr v-if="!alunos.length">
                    <td colspan="3">Nenhum aluno cadastrado.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const alunos = ref([]);
const editingId = ref(null);
const validationErrors = ref([]);
const message = ref(null);

const alunoForm = ref({
    nome: '',
    email: '',
});

function showMessage(text, type = 'success') {
    message.value = { text, type };
}

function resetForm() {
    editingId.value = null;
    alunoForm.value = {
        nome: '',
        email: '',
    };
}

async function loadAlunos() {
    try {
        const response = await axios.get('/api/alunos');
        alunos.value = response.data;
    } catch (error) {
        showMessage('Não foi possível carregar a lista de alunos.', 'error');
    }
}

async function submitAluno() {
    validationErrors.value = [];

    try {
        const payload = {
            nome: alunoForm.value.nome,
            email: alunoForm.value.email,
        };

        if (editingId.value) {
            const response = await axios.put(`/api/alunos/${editingId.value}`, payload);
            showMessage(response.data.mensagem || 'Aluno atualizado com sucesso!', 'success');
        } else {
            const response = await axios.post('/api/alunos', payload);
            showMessage(response.data.mensagem || 'Aluno cadastrado com sucesso!', 'success');
        }

        resetForm();
        await loadAlunos();
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            validationErrors.value = Object.values(errors).flat();
            showMessage('Verifique os dados informados.', 'error');
        } else if (error.response?.status === 404) {
            showMessage('Aluno não encontrado.', 'error');
        } else {
            showMessage('Não foi possível concluir a operação. Tente novamente.', 'error');
        }
    }
}

function editarAluno(aluno) {
    editingId.value = aluno.id;
    alunoForm.value = {
        nome: aluno.nome,
        email: aluno.email,
    };
}

async function excluirAluno(aluno) {
    if (!confirm(`Deseja excluir o aluno ${aluno.nome}?`)) {
        return;
    }

    try {
        const response = await axios.delete(`/api/alunos/${aluno.id}`);
        showMessage(response.data.mensagem || 'Aluno excluído com sucesso!', 'success');
        await loadAlunos();
    } catch (error) {
        if (error.response?.status === 404) {
            showMessage('Aluno inexistente ou já removido.', 'error');
        } else {
            showMessage('Não foi possível excluir o aluno.', 'error');
        }
    }
}

onMounted(() => {
    loadAlunos();
});
</script>

<style scoped>
.container {
    max-width: 900px;
    margin: 40px auto;
    font-family: Arial, sans-serif;
}

h1 {
    margin-bottom: 20px;
}

form {
    display: grid;
    gap: 12px;
    margin-bottom: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: 700;
    margin-bottom: 6px;
}

input {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.actions {
    display: flex;
    gap: 10px;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    cursor: pointer;
}

.btn-primary {
    background: #0d6efd;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-small {
    padding: 6px 10px;
    margin-right: 8px;
}

.message {
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 14px;
}

.message.success {
    background: #d1e7dd;
    color: #0f5132;
}

.message.error {
    background: #f8d7da;
    color: #842029;
}

.error-list {
    color: #b02a37;
    margin-bottom: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #dee2e6;
    padding: 12px;
    text-align: left;
}

th {
    background: #f8f9fa;
}
</style>
