<template>
  <MonacoEditor
    :value="modelValue"
    :language="language"
    :theme="theme"
    :options="editorOptions"
    :height="height"
    :width="width"
    @update:value="onChange"
    @editor-mounted="onEditorMounted"
  />
</template>

<script>
import MonacoEditor from 'monaco-editor-vue3';

export default {
  name: 'MonacoEditor',
  components: { MonacoEditor },
  props: {
    modelValue: {
      type: String,
      default: '',
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    language: {
      type: String,
      default: 'plaintext', // Use plaintext for SMS content
    },
    theme: {
      type: String,
      default: 'vs-dark', // Monaco's dark theme (similar to dracula)
    },
    placeholder: {
      type: String,
      default: 'Enter SMS campaign description...',
    },
    height: {
      type: String,
      default: '150px',
    },
    width: {
      type: String,
      default: '100%',
    },
  },
  emits: ['update:modelValue', 'change', 'editor-mounted'],
  computed: {
    editorOptions() {
      return {
        readOnly: this.readonly,
        minimap: { enabled: false }, // Disable minimap for SMS editor
        lineNumbers: 'on', // Show line numbers
        wordWrap: 'on', // Wrap long lines for SMS
        fontSize: 14, // Match Tailwind text-sm
        automaticLayout: true, // Auto-resize with container
        padding: { top: 8, bottom: 8 }, // Add padding
        placeholder: this.placeholder, // Set placeholder
      };
    },
  },
  methods: {
    onChange(value) {
      let newValue = value;
      if (value.length > 160) {
        newValue = value.substring(0, 160); // Enforce 160-char limit
        console.warn('Content truncated to 160 characters');
      }
      this.$emit('update:modelValue', newValue);
      this.$emit('change', newValue);
    },
    onEditorMounted(editor) {
      this.$emit('editor-mounted', editor);
      console.log('Monaco Editor mounted:', editor);
    },
  },
};
</script>

<style scoped>
/* Style the Monaco Editor container */
:deep(.monaco-editor) {
  border: 1px solid #44475a; /* Dark border (similar to dracula) */
  border-radius: 0.375rem; /* Tailwind rounded */
  background: #1e1e1e; /* vs-dark background */
  color: #d4d4d4; /* vs-dark foreground */
  min-height: v-bind(height);
  width: v-bind(width);
  padding: 0.5rem; /* Tailwind p-2 */
  box-sizing: border-box;
  z-index: 10; /* Increased to prevent overlap with modal */
}

/* Style line numbers */
:deep(.line-numbers) {
  background: #1e1e1e; /* Match vs-dark */
  color: #858585; /* Dimmed line numbers */
}

/* Style placeholder */
:deep(.monaco-editor .mtk1:empty:before) {
  content: v-bind(placeholder);
  color: #858585; /* Dimmed placeholder */
  opacity: 0.7;
  position: absolute;
  pointer-events: none;
  padding-left: 0.5rem;
}

/* Readonly state */
:deep(.monaco-editor[aria-readonly='true']) {
  background: #2a2a2a; /* Slightly lighter for readonly */
  cursor: not-allowed;
}

/* Ensure editor content area is styled */
:deep(.monaco-editor .view-lines) {
  font-size: 0.875rem; /* Tailwind text-sm */
  line-height: 1.5; /* Tailwind leading-normal */
}

/* Prevent Tailwind conflicts */
:deep(.monaco-editor .view-line) {
  padding: 0 !important; /* Override any Tailwind padding */
}
</style>
