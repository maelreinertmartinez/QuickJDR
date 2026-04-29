<!-- component for party cards -->
<script setup>
defineProps({
  partyId: { type: Number },
  partyMj: { type: String },
  nbPlayers: { type: Number },
})
</script>

<template>
  <!-- card -->
  <div
    class="bg-green-jdr rounded-lg shadow-md p-8 w-full max-w-xs border-olive-jdr/40 border-2 hover:border-orange-jdr"
  >
    <!-- party id -->
    <h1 class="text-2xl text-creamy-jdr mb-2 font-bold bg-orange-jdr rounded-lg px-2">
      Partie #{{ partyId }}
    </h1>
    <!-- party owner -->
    <h2 class="text-xl text-creamy-jdr font-semibold p-2">Hébergée par:</h2>
    <h2 class="text-xl text-creamy-jdr font-semibold p-2 text-center bg-orange-jdr rounded-lg">
      {{ partyMj }}
    </h2>
    <!-- party population -->
    <p class="text-creamy-jdr p-2">
      Nombre de joueurs: {{ nbPlayers
      }}<!-- {{ party.character_count }}-->
    </p>
  </div>
</template>

<style scoped></style>
