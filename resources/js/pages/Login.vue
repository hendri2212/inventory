<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

const visible = ref(false);
const form = useForm({ email: '', password: '', remember: false });
const submit = () =>
    form.post('/login', { onFinish: () => form.reset('password') });
</script>

<template>
    <Head title="Masuk" />
    <main class="login-shell">
        <section class="login-story" aria-label="Inventory">
            <a class="inventory-brand" href="/login"
                ><span class="brand-mark">▦</span> inventory<span
                    class="brand-dot"
                    >.</span
                ></a
            >
            <div class="story-content">
                <span class="eyebrow">RUANG KERJA YANG LEBIH TERATUR</span>
                <h1>Setiap aset.<br />Dalam kendali<span>.</span></h1>
                <p>
                    Awali pengelolaan inventaris yang lebih rapi, terarah, dan
                    mudah dari satu tempat.
                </p>
                <div class="asset-visual" aria-hidden="true">
                    <div class="visual-top">
                        <span class="visual-icon">▦</span
                        ><span
                            >Workspace inventory<small
                                >Semua tersusun dengan baik</small
                            ></span
                        ><span class="status-dot"></span>
                    </div>
                    <div class="visual-line">
                        <span class="item-icon">▱</span
                        ><span
                            >Aset & perlengkapan<small
                                >Terpusat dalam satu ruang</small
                            ></span
                        ><span class="visual-check">✓</span>
                    </div>
                    <div class="visual-line">
                        <span class="item-icon">⌑</span
                        ><span
                            >Informasi terorganisir<small
                                >Siap saat Anda membutuhkan</small
                            ></span
                        ><span class="visual-check">✓</span>
                    </div>
                    <div class="visual-bottom">
                        <span class="status-dot"></span> Lebih teratur. Lebih
                        produktif.
                    </div>
                </div>
            </div>
            <div class="story-footer">
                DIRANCANG UNTUK KERJA YANG LEBIH BAIK
                <span>© {{ new Date().getFullYear() }} Inventory</span>
            </div>
        </section>
        <section class="login-panel">
            <div class="login-form-wrap">
                <span class="login-badge">WORKSPACE ACCESS</span>
                <h2>Selamat datang kembali</h2>
                <p class="login-intro">
                    Masuk untuk melanjutkan ke ruang kerja Anda.
                </p>
                <form class="login-form" @submit.prevent="submit">
                    <div class="login-field">
                        <label for="email" class="form-label"
                            >Alamat email</label
                        >
                        <input
                            id="email"
                            v-model="form.email"
                            v-focus
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.email }"
                            type="email"
                            autocomplete="username"
                            placeholder="nama@perusahaan.com"
                            required
                            :aria-invalid="Boolean(form.errors.email)"
                            :aria-describedby="
                                form.errors.email ? 'email-error' : undefined
                            "
                        />
                        <div
                            v-if="form.errors.email"
                            id="email-error"
                            class="invalid-feedback"
                            role="alert"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>
                    <div class="login-field">
                        <label for="password" class="form-label"
                            >Kata sandi</label
                        >
                        <div class="password-wrap">
                            <input
                                id="password"
                                v-model="form.password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                :type="visible ? 'text' : 'password'"
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                required
                                :aria-invalid="Boolean(form.errors.password)"
                                :aria-describedby="
                                    form.errors.password
                                        ? 'password-error'
                                        : undefined
                                "
                            />
                            <button
                                type="button"
                                class="password-toggle"
                                :aria-pressed="visible"
                                aria-controls="password"
                                @click="visible = !visible"
                            >
                                {{ visible ? 'Sembunyikan' : 'Lihat' }}
                            </button>
                        </div>
                        <div
                            v-if="form.errors.password"
                            id="password-error"
                            class="field-error"
                            role="alert"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>
                    <div class="form-check remember-row">
                        <input
                            id="remember"
                            v-model="form.remember"
                            class="form-check-input"
                            type="checkbox"
                        /><label for="remember" class="form-check-label"
                            >Ingat saya di perangkat ini</label
                        >
                    </div>
                    <button
                        class="btn login-submit w-100"
                        type="submit"
                        :disabled="form.processing"
                    >
                        {{
                            form.processing
                                ? 'Memproses…'
                                : 'Masuk ke ruang kerja'
                        }}
                        <span aria-hidden="true">→</span>
                    </button>
                </form>
                <div class="login-help">
                    Belum memiliki akses?
                    <span>Hubungi administrator Anda.</span>
                </div>
            </div>
            <div class="panel-footer">
                <span class="secure-icon" aria-hidden="true">◇</span> Akses aman
                untuk inventaris Anda
            </div>
        </section>
    </main>
</template>

<style scoped>
.login-shell {
    color-scheme: light;
    min-height: 100svh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #fbfcfa;
    color: #192b29;
    font-family: 'Instrument Sans', sans-serif;
}
.login-story {
    background: #123c35;
    color: #fff;
    padding: 42px 56px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}
.login-story:before {
    content: '';
    position: absolute;
    width: 520px;
    height: 520px;
    border: 1px solid #ffffff09;
    border-radius: 50%;
    right: -240px;
    top: 10%;
    box-shadow:
        0 0 0 85px #ffffff03,
        0 0 0 170px #ffffff02;
    pointer-events: none;
}
.inventory-brand {
    font-size: 25px;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}
.brand-mark {
    color: #cae5b1;
    font-size: 34px;
}
.brand-dot {
    color: #c9e6ac;
    margin-left: -10px;
}
.story-content {
    margin: auto 0;
    padding: 65px 0;
    position: relative;
    max-width: 440px;
}
.eyebrow {
    font-size: 10px;
    letter-spacing: 2px;
    color: #b7cdbd;
    font-weight: 600;
}
.story-content h1 {
    font-size: clamp(42px, 4.5vw, 66px);
    line-height: 1.09;
    letter-spacing: -2.7px;
    margin: 23px 0;
    font-weight: 500;
}
.story-content h1 span {
    color: #c9e6ac;
}
.story-content > p {
    color: #b5c8c1;
    font-size: 15px;
    line-height: 1.8;
    max-width: 340px;
}
.asset-visual {
    margin-top: 40px;
    border: 1px solid #ffffff20;
    border-radius: 18px;
    background: linear-gradient(130deg, #ffffff0d, #ffffff03);
    padding: 22px;
    box-shadow: 0 24px 60px #06241e40;
    transform: rotate(-2deg);
}
.visual-top,
.visual-line {
    display: flex;
    align-items: center;
    gap: 13px;
    font-size: 13px;
}
.visual-top {
    padding-bottom: 20px;
    border-bottom: 1px solid #ffffff13;
    font-weight: 600;
}
.visual-top small,
.visual-line small {
    display: block;
    font-size: 10px;
    color: #adc4ba;
    margin-top: 5px;
    font-weight: 400;
}
.visual-icon,
.item-icon {
    display: grid;
    place-items: center;
    border-radius: 10px;
    width: 38px;
    height: 38px;
    background: #ffffff0c;
    color: #cce6b9;
    font-size: 23px;
}
.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #c9e6ac;
    display: inline-block;
    flex-shrink: 0;
}
.visual-top > .status-dot {
    margin-left: auto;
}
.visual-line {
    padding-top: 20px;
}
.visual-check {
    margin-left: auto;
    color: #c9e6ac;
}
.visual-bottom {
    font-size: 10px;
    color: #b9cfc3;
    border-top: 1px solid #ffffff13;
    margin-top: 20px;
    padding-top: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.story-footer {
    position: relative;
    display: flex;
    justify-content: space-between;
    gap: 16px;
    font-size: 8px;
    letter-spacing: 1px;
    color: #a0bcb0;
}
.story-footer span {
    letter-spacing: 0;
    white-space: nowrap;
}
.login-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 70px 48px 35px;
}
.login-form-wrap {
    width: 100%;
    max-width: 390px;
    margin: auto 0;
}
.login-badge {
    font-size: 10px;
    letter-spacing: 1.7px;
    color: #597b63;
    background: #eaf0e6;
    border: 1px solid #dee7d8;
    padding: 7px 11px;
    border-radius: 6px;
    font-weight: 600;
    display: inline-block;
}
.login-form-wrap h2 {
    font-size: 30px;
    font-weight: 600;
    letter-spacing: -1.1px;
    margin: 25px 0 12px;
    line-height: 1.2;
}
.login-intro {
    font-size: 14px;
    color: #79837e;
    line-height: 1.7;
}
.login-form {
    margin-top: 36px;
}
.login-field {
    margin-bottom: 24px;
}
.form-label {
    color: #294537;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 10px;
    display: block;
}
.form-control {
    min-height: 52px;
    border: 1px solid #dce2db;
    border-radius: 9px;
    background: #fff;
    font-size: 14px;
    padding: 13px 15px;
    width: 100%;
    color: #192b29;
}
.form-control::placeholder {
    color: #a0a8a2;
}
.form-control:focus {
    border-color: #527965;
    box-shadow: 0 0 0 3px #52796518;
    outline: none;
}
.form-control.is-invalid {
    border-color: #b94444;
}
.password-wrap {
    position: relative;
}
.password-wrap input {
    padding-right: 105px;
    background-image: none;
}
.password-toggle {
    position: absolute;
    right: 14px;
    top: 0;
    height: 52px;
    font-size: 11px;
    color: #577263;
    background: none;
    border: 0;
    cursor: pointer;
}
.remember-row {
    font-size: 12px;
    color: #6e7b73;
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 27px;
    padding-left: 0;
}
.form-check-input {
    float: none;
    margin: 0;
    width: 15px;
    height: 15px;
    accent-color: #173f35;
}
.login-submit {
    background: #173f35;
    color: #fff;
    min-height: 52px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    border: 0;
    transition: background 0.2s;
}
.login-submit:hover,
.login-submit:focus-visible {
    background: #245847;
    color: #fff;
}
.login-submit:disabled {
    opacity: 0.65;
    background: #173f35;
    color: #fff;
}
.login-submit span {
    font-size: 20px;
}
.login-help {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid #e7ebe5;
    text-align: center;
    font-size: 11px;
    color: #8a938d;
    line-height: 1.8;
}
.login-help span {
    color: #3c5c4c;
    display: block;
}
.panel-footer {
    font-size: 10px;
    color: #98a198;
    margin-top: 55px;
    display: flex;
    gap: 7px;
    align-items: center;
}
.secure-icon {
    font-size: 18px;
}
.invalid-feedback,
.field-error {
    color: #b94444;
    font-size: 12px;
    margin-top: 7px;
    display: block;
}
@media (min-width: 1500px) {
    .login-story {
        padding-left: 90px;
    }
    .story-content {
        max-width: 480px;
    }
}
@media (max-width: 900px) {
    .login-story {
        padding: 32px;
    }
    .story-content h1 {
        font-size: 44px;
    }
    .story-footer {
        flex-direction: column;
    }
    .login-panel {
        padding: 45px 28px;
    }
    .login-form-wrap h2 {
        font-size: 26px;
    }
}
@media (max-width: 800px) {
    .login-shell {
        grid-template-columns: 1fr;
    }
    .login-story {
        padding: 22px 26px;
    }
    .story-content,
    .story-footer {
        display: none;
    }
    .login-panel {
        padding: 45px 26px 25px;
        min-height: calc(100svh - 90px);
    }
    .login-form-wrap {
        max-width: 400px;
    }
    .inventory-brand {
        font-size: 22px;
    }
    .panel-footer {
        margin-top: 40px;
    }
}
</style>
