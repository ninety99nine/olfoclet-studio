<template>
    <!--
        Component Referece: https://flowbite.com/docs/components/spinner/
     -->
    <div :class="sizeClass+'inline-block animate-spin rounded-full border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]'"></div>

</template>

<script>
export default {
    props: {
        size: {
            type: String,
            default: 'md',
        }
    },
    computed: {
        sizeClass() {
            if(this.size == 'sm') {

                return 'w-3 h-3 border ';

            }else if(this.size == 'md') {

                return 'w-6 h-6 border-2 ';

            }
        }
    }
};
</script>
