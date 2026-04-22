<template>
<div>
    <h3>Ajout d'employes avec droit d'édition pour le service<br>{{ lesDatas.descriptionService }}</h3>
    <input type="text" v-model="state.critere" v-on:keyup="listeEmployes" placeholder="- nom pr&eacute;nom login -">&nbsp;
    <input type="checkbox" id="chkRetInactif" v-model="state.bRetInactif" v-on:change="listeEmployes"><label for="chkRetInactif">avec les employ&eacute;s d&eacute;sactiv&eacute;s</label>
    <ul>
        <template v-for="employe in employesChoix" v-bind:key="employe.idemploye">
            <li>
                <span 
                    v-bind:id="'liemploye' + employe.idemploye" 
                    v-bind:class="'empbactif' + employe.bactif" 
                    v-bind:title="employe.directionabr + '-' + employe.serviceabr + ' - ' + employe.unite"
                    v-on:click="employeEditionAjout(employe.idemploye)"
                >
                    {{employe.nom}} {{employe.prenom}}&nbsp; 
                    <span class="emplogin">({{employe.login}})</span>
                </span>
            </li>
        </template>
    </ul>
</div>
</template>
<script setup lang="ts">
import type { Employe } from '@/stores/datastore.ts'
import type { EmployeChoix, ApiResponseMessage, ApiResponseEmpS } from '@/axioscalls.ts'
import {reactive, ref} from 'vue'
import { useDataStore } from '@/stores/datastore.ts'
import { getEmployesListe, sauveDroitComptableGestion, getComptableGestion } from '@/axioscalls.ts'
const state = reactive({
    critere: "",
    bRetInactif: false
})
const lesDatas = useDataStore()
const employesChoix = ref<EmployeChoix[]>([])

async function listeEmployes() {
    employesChoix.value = await getEmployesListe(state.critere, state.bRetInactif)
    console.log(employesChoix.value)
}

const employeEditionAjout = async (employeId: number) => {
    //Un service doit être choisi
    if (lesDatas.idService > 0) {
        //L'employé choisi ne doit pas déjà être dans les éditeurs autorisés du service
        const employeDejaChoisi: Employe | undefined = lesDatas.employesEdition.find(employe => employe.id === employeId)
        if (!employeDejaChoisi) {
            console.log(`Employé à ajouter: ${employeId} ${lesDatas.idService}`)
            const response: ApiResponseMessage = await sauveDroitComptableGestion(lesDatas.idService, employeId)
            const responseReaff: ApiResponseEmpS = await getComptableGestion(lesDatas.idService)
            lesDatas.employesEdition = responseReaff.success && responseReaff.data ? responseReaff.data : []
        }     
    }
}

</script>