<template>
  <transition name="fade">
    <div v-if="visible && message" :class="['flash-message', type]">
      {{ message }}
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    flash: Object,
    duration: {
      type: Number,
      default: 5000,
    },
  },
  data() {
    return {
      visible: false,
    };
  },
  computed: {
    message() {
      return (
        this.flash?.success ||
        this.flash?.error ||
        this.flash?.warning ||
        this.flash?.info ||
        null
      );
    },
    type() {
      if (this.flash?.success) return "success";
      if (this.flash?.error) return "error";
      if (this.flash?.warning) return "warning";
      if (this.flash?.info) return "info";
      return "";
    },
  },
  watch: {
    message(newVal) {
      if (newVal) {
        this.visible = true;
        setTimeout(() => {
          this.visible = false;
        }, this.duration);
      }
    },
  },
};
</script>

<style scoped>
.flash-message {
  position: fixed;
  top: 1rem;
  right: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 0.375rem;
  color: white;
  z-index: 9999;
  box-shadow: 0 2px 8px rgb(0 0 0 / 0.15);
  user-select: none;
  font-weight: 600;
  min-width: 200px;
  max-width: 300px;
  text-align: center;
}

.success {
  background-color: #48bb78;
}
.error {
  background-color: #f56565;
}
.warning {
  background-color: #ed8936;
}
.info {
  background-color: #4299e1;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
