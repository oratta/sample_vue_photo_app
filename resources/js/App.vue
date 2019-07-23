<template>
    <div>
        <header>
            <Navbar />
        </header>
        <main>
            <div class="container">
                <RouterView />
            </div>
        </main>
        <Footer />
    </div>
</template>

<script>
    import Navbar from './components/Navbar.vue'
    import Footer from './components/Footer.vue'

    import { INTERNAL_SERVER_ERROR, UNAUTHORIZED } from "./util";

    export default {
        components: {
            Navbar,
            Footer
        },
        computed: {
            errorCode () {
                return this.$store.state.error.code
            }
        },
        watch: {
            errorCode: {
                async handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    } else if (val === UNAUTHORIZED) {
                        // トークンをリフレッシュ
                        await axios.get('/api/refresh-token')
                        // ストアのuserをクリア
                        this.$store.commit('auth/setUser', null)
                        // ログイン画面へ
                        this.$router.push('/login')
                    }
                },
                immediate: true
            },
            $route () {
                this.$store.commit('error/setCode', null)
            }
        }
    }
</script>