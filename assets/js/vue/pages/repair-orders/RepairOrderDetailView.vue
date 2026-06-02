<template>
  <div>
    <router-link to="/" class="btn btn-link px-0 mb-3 text-decoration-none">
      ← Retour à la liste
    </router-link>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status"></div>
    </div>

    <div v-else-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-else-if="order" class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ order.reference }}</h5>
        <span :class="'status-badge status-' + order.status">{{ statusLabel(order.status) }}</span>
      </div>
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-md-6"><strong>Client :</strong> {{ order.customer?.name ?? '—' }}</div>
          <div class="col-md-6"><strong>Total :</strong> {{ order.totalAmount.toFixed(2) }} €</div>
        </div>
        <p class="mb-3"><strong>Description :</strong> {{ order.description ?? '—' }}</p>

        <hr />

        <div class="container my-4">
          <!-- En-tête -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">DEVIS</h1>

            <div class="text-end">
              <div>
                <strong>Référence :</strong> {{ order.quote?.reference }}
              </div>
              <div>
                <strong>Date :</strong> {{ order.quote?.createdAt }}
              </div>
            </div>
          </div>      

        <!-- Tableau -->
        <div class="card">
          <div class="card-body p-0">
            <table class="table table-bordered table-striped align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Désignation</th>
                  <th class="text-end">Montant HT</th>
                  <th class="text-center">Quantité</th>
                  <th class="text-center">TVA (%)</th>
                  <th class="text-center">Remise (%)</th>
                  <th class="text-end">Montant TTC</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(line, index) in order.quote.lines"
                  :key="index"
                >
                  <td>{{ line.displayLabel }}</td>

                  <td class="text-end">
                    {{ formatPrice(line.priceExcludingTaxes) }}
                  </td>

                  <td class="text-center">
                    {{ line.quantity }}
                  </td>

                  <td class="text-center">
                    {{ line.taxPercentage }}
                  </td>

                  <td class="text-center">
                    {{ line.discountPercentage }}
                  </td>

                  <td class="text-end">
                    {{ formatPrice(line.priceWithTaxes) }}
                  </td>
                </tr>
              </tbody>

              <tfoot>
                <tr>
                  <td colspan="5" class="text-end fw-bold">
                    Total HT
                  </td>

                  <td class="text-end fw-bold">
                    {{ formatPrice(order.quote?.totalExcludingTaxes) }}
                  </td>
                </tr>

                <tr class="table-secondary">
                  <td colspan="5" class="text-end fw-bold">
                    Total TTC
                  </td>

                  <td class="text-end fw-bold">
                    {{ formatPrice(order.quote?.totalWithTaxes) }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { statusLabel } from './status'

const props = defineProps<{ id: string }>()

interface Line {
  displayLabel: string
  priceExcludingTaxes: number
  quantity: number
  taxPercentage: number
  discountPercentage: number
  priceWithTaxes: number
}

interface RepairOrder {
  id: number
  reference: string
  status: string
  totalAmount: number
  description: string | null
  customer: { name: string; email: string | null; phone?: string | null } | null
  quote: { reference: string, createdAt: string, lines: Array<Line>, totalExcludingTaxes: number, totalWithTaxes:number }
}

const order = ref<RepairOrder | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

async function load(): Promise<void> {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get<RepairOrder>(`/api/repair-orders/${props.id}`)
    order.value = data
  } catch {
    error.value = 'Ordre de réparation introuvable'
  } finally {
    loading.value = false
  }
}

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(value)
}
  
onMounted(load)
</script>
