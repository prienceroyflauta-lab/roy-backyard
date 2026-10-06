<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
$products = $products ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        :root {
            color-scheme: dark;
            --navy: #080d21;
            --navy-soft: #111b3e;
            --blue: #1b3476;
            --cyan: #35d7ed;
            --gold: #ffc928;
            --paper: #070b1a;
            --ink: #f4f7ff;
            --muted: #aab7d8;
            --line: #293967;
            --white: #f4f7ff;
        }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(ellipse at 50% -10%, rgba(34, 87, 182, .3), transparent 42rem),
                radial-gradient(ellipse at 12% 55%, rgba(37, 19, 94, .2), transparent 34rem),
                var(--paper);
            color: var(--ink);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }
        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 400' fill='none'%3E%3Cpath d='M40 200C112 96 204 44 300 44s188 52 260 156c-72 104-164 156-260 156S112 304 40 200Z' stroke='%2335d7ed' stroke-opacity='.15' stroke-width='3'/%3E%3Ccircle cx='300' cy='200' r='91' stroke='%23ffc928' stroke-opacity='.18' stroke-width='12'/%3E%3Ccircle cx='300' cy='200' r='68' fill='%231b3476' fill-opacity='.25' stroke='%2335d7ed' stroke-opacity='.2' stroke-width='4'/%3E%3Ccircle cx='300' cy='200' r='33' fill='%23070b1a' fill-opacity='.65'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center 53%;
            background-size: min(880px, 92vw) auto;
            content: "";
            opacity: .52;
            pointer-events: none;
        }
        .topbar {
            position: relative;
            z-index: 2;
            background: rgba(7, 11, 26, .94);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        }
        .topbar::after {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--cyan), #809aff 48%, var(--gold));
            content: "";
        }
        .topbar-inner, main {
            width: min(1180px, calc(100% - 48px));
            margin: 0 auto;
        }
        .topbar-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1.3px;
            text-decoration: none;
            text-transform: uppercase;
        }
        .eye-mark {
            width: 44px;
            height: 36px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(66, 215, 232, .48);
            border-radius: 50% 50% 46% 46%;
            background: rgba(66, 215, 232, .08);
            box-shadow: 0 0 22px rgba(66, 215, 232, .15);
        }
        .eye-mark svg { width: 31px; height: 24px; }
        .connection {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #d1dcf7;
            font-size: 12px;
            font-weight: 650;
            letter-spacing: .6px;
            text-transform: uppercase;
        }
        .connection::before {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 12px var(--cyan);
            content: "";
        }
        main { padding: 38px 0 72px; }
        .intro {
            position: relative;
            min-height: 260px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            padding: 34px 38px;
            border: 1px solid rgba(114, 152, 237, .28);
            border-radius: 20px;
            background:
                radial-gradient(ellipse at 82% 50%, rgba(53, 215, 237, .09), transparent 18rem),
                linear-gradient(120deg, rgba(12, 20, 47, .97), rgba(16, 31, 71, .94));
            box-shadow: 0 20px 55px rgba(0, 0, 0, .27), inset 0 1px rgba(255, 255, 255, .04);
            color: var(--white);
        }
        .intro-content {
            position: relative;
            z-index: 1;
            width: 100%;
        }
        .intro .toolbar { position: relative; z-index: 1; justify-content: flex-end; }
        .hero-eye {
            display: none;
        }
        .hero-eye svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }
        .hero-eye .eye-outline {
            fill: rgba(17, 39, 92, .42);
            stroke: var(--cyan);
            stroke-width: 3;
        }
        .hero-eye .iris-ring { fill: #173870; stroke: var(--gold); stroke-width: 5; }
        .hero-eye .iris { fill: var(--cyan); }
        .hero-eye .pupil { fill: #071127; }
        @keyframes eye-arrive {
            0% { opacity: 0; transform: scale(.12); filter: drop-shadow(0 0 0 rgba(53, 215, 237, 0)); }
            65% { opacity: 1; transform: scale(1.14); }
            100% { opacity: 1; transform: scale(1); filter: drop-shadow(0 0 23px rgba(53, 215, 237, .42)); }
        }
        .intro h1 { text-shadow: 0 0 28px rgba(53, 215, 237, .12); }
        .intro .eyebrow {
            text-shadow: 0 0 16px rgba(255, 201, 40, .28);
        }
        .eyebrow {
            margin: 0 0 10px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        h1 {
            margin: 0;
            font-size: clamp(32px, 5vw, 48px);
            letter-spacing: -1.8px;
        }
        .subtitle {
            max-width: 560px;
            margin: 11px 0 0;
            color: #d1dcf7;
            font-size: 14px;
            line-height: 1.65;
            margin-right: auto;
            margin-left: auto;
        }
        .count {
            position: relative;
            z-index: 1;
            flex: 0 0 auto;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 12px;
            background: rgba(9, 20, 54, .5);
            color: var(--white);
            font-size: 12px;
            font-weight: 750;
            letter-spacing: .5px;
            backdrop-filter: blur(8px);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(245px, 1fr));
            gap: 18px;
        }
        .product-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(117, 145, 205, .2);
            border-radius: 15px;
            background: linear-gradient(145deg, rgba(17, 27, 59, .96), rgba(10, 18, 41, .96));
            box-shadow: 0 12px 30px rgba(0, 0, 0, .24), inset 0 1px rgba(255, 255, 255, .035);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .product-card::before {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--cyan), var(--blue), var(--gold), transparent);
            content: "";
        }
        .product-card:hover {
            border-color: rgba(53, 215, 237, .52);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .34), 0 0 20px rgba(53, 215, 237, .08);
            transform: translateY(-3px);
        }
        .product-visual {
            position: relative;
            height: 130px;
            display: grid;
            place-items: center;
            overflow: hidden;
            background:
                radial-gradient(circle at 50% 50%, rgba(53, 215, 237, .12), transparent 32%),
                linear-gradient(135deg, #101d3f, #111b36 60%, #0e263e);
            color: var(--cyan);
            font-size: 44px;
            font-weight: 850;
        }
        .product-visual::before {
            position: absolute;
            width: 124px;
            height: 124px;
            border: 1px solid rgba(53, 215, 237, .12);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(53, 215, 237, .1), transparent 68%);
            content: "";
        }
        .product-visual { isolation: isolate; }
        .product-visual > * { position: relative; z-index: 1; }
        .product-body { padding: 18px; }
        .product-body h2 {
            overflow-wrap: anywhere;
            margin: 0;
            color: var(--ink);
            font-size: 17px;
            letter-spacing: -.25px;
        }
        .description {
            min-height: 42px;
            margin: 9px 0 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }
        .product-details {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid rgba(122, 157, 221, .16);
        }
        .detail-label {
            display: block;
            margin-bottom: 5px;
            color: #91a4d1;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.1px;
            text-transform: uppercase;
        }
        .price { color: var(--gold); font-size: 19px; font-weight: 800; }
        .quantity { color: #c0cceb; font-size: 13px; text-align: right; }
        .empty-state {
            padding: 52px 24px;
            border: 1px dashed #3a5790;
            border-radius: 14px;
            background: rgba(16, 26, 55, .94);
            color: var(--muted);
            text-align: center;
        }
        .actions { display: flex; gap: 8px; }
        button {
            border: 0;
            border-radius: 9px;
            padding: 10px 14px;
            background: var(--blue);
            color: var(--white);
            cursor: pointer;
            font: inherit;
            font-size: 13px;
            font-weight: 750;
            transition: background .15s ease, transform .15s ease;
        }
        button:hover:not(:disabled) { background: #173879; transform: translateY(-1px); }
        button.secondary { background: #172951; color: #d6e3ff; border: 1px solid #304a7b; }
        button.secondary:hover:not(:disabled) { background: #203b70; }
        button.danger { background: #45213c; color: #ffb5c5; border: 1px solid #753752; }
        button.danger:hover:not(:disabled) { background: #5a294c; }
        button:disabled { cursor: wait; opacity: .6; }
        .toolbar { display: flex; align-items: center; gap: 12px; }
        .identity { color: #e0e7fa; font-size: 13px; font-weight: 650; }
        .notice {
            margin: 0 0 20px;
            padding: 13px 15px;
            border: 1px solid #79354e;
            border-radius: 10px;
            background: #32172c;
            color: #ffc0cc;
            font-size: 14px;
        }
        .notice.success {
            border-color: #27765c;
            background: #102d31;
            color: #92f0cc;
        }
        .auth-wrap {
            position: relative;
            width: min(480px, calc(100% - 32px));
            margin: 76px auto;
            animation: page-arrive .5s ease both;
        }
        @keyframes page-arrive {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .auth-card, .form-card {
            padding: 29px;
            border: 1px solid #2d4478;
            border-radius: 18px;
            background: linear-gradient(145deg, #111d40, #0c1530 72%);
            box-shadow: 0 18px 48px rgba(0, 0, 0, .38), inset 0 0 36px rgba(53, 215, 237, .025);
        }
        .auth-card { border-top: 4px solid var(--cyan); }
        .auth-card h1, .form-card h2 { margin: 0 0 8px; color: var(--ink); font-size: 25px; }
        .auth-card > p, .form-card > p { margin: 0 0 22px; color: var(--muted); line-height: 1.5; }
        label { display: block; margin: 15px 0 6px; color: #d1ddfa; font-size: 12px; font-weight: 750; }
        input, textarea {
            width: 100%;
            border: 1px solid #344b7c;
            border-radius: 9px;
            padding: 11px 12px;
            outline: none;
            background: #080f25;
            color: #f4f7ff;
            font: inherit;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        input:focus, textarea:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(66, 121, 219, .13);
        }
        textarea { min-height: 95px; resize: vertical; }
        .auth-card button, .form-card button[type="submit"] { width: 100%; margin-top: 20px; }
        .auth-switch { margin-top: 18px !important; text-align: center; font-size: 13px; }
        .auth-switch button { padding: 0; background: transparent; color: var(--cyan); }
        .auth-switch button:hover:not(:disabled) { background: transparent; text-decoration: underline; transform: none; }
        .form-card { margin: 0 0 24px; border-top: 4px solid var(--gold); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .form-actions { display: flex; gap: 8px; }
        .form-actions button { flex: 1; }
        [v-cloak] { display: none; }
        @media (max-width: 700px) {
            .topbar-inner, main { width: min(100% - 30px, 1180px); }
            main { padding-top: 24px; }
            .intro { min-height: 215px; grid-template-columns: 1fr; gap: 18px; padding: 26px 23px; }
            .intro-content { max-width: 100%; }
            .toolbar { align-items: center; flex-wrap: wrap; }
            .intro .toolbar { justify-content: flex-start; }
            .form-grid { grid-template-columns: 1fr; }
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(215px, 1fr)); }
        }
        @media (max-width: 420px) {
            .brand { font-size: 12px; letter-spacing: .8px; }
            .eye-mark { width: 38px; height: 32px; }
            .connection { font-size: 10px; }
            .intro-content { max-width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <div id="app" v-cloak>
        <header class="topbar">
            <div class="topbar-inner">
                <a class="brand" href="/">
                    <span class="eye-mark" aria-hidden="true">
                        <svg viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 16C8 7.5 15.3 3 24 3s16 4.5 22 13c-6 8.5-13.3 13-22 13S8 24.5 2 16Z" stroke="#42D7E8" stroke-width="2.5"/>
                            <circle cx="24" cy="16" r="8" stroke="#FFD45E" stroke-width="2.5"/>
                            <circle cx="24" cy="16" r="3.5" fill="#42D7E8"/>
                        </svg>
                    </span>
                    Bahay Catalog
                </a>
                <div v-if="user" class="toolbar">
                    <span class="identity">{{ user.username }}</span>
                    <button class="secondary" @click="logout">Log out</button>
                </div>
                <span v-else class="connection">LavaLust API</span>
            </div>
        </header>

        <section v-if="!user" class="auth-wrap">
            <div class="auth-card">
                <p class="eyebrow">Product management</p>
                <h1>{{ registering ? 'Create account' : 'Welcome back' }}</h1>
                <p>{{ registering ? 'Register an account to manage products.' : 'Log in to view and manage your products.' }}</p>
                <div v-if="error" class="notice">{{ error }}</div>
                <div v-if="notice" class="notice success">{{ notice }}</div>
                <form @submit.prevent="authenticate">
                    <template v-if="registering">
                        <label for="username">Username</label>
                        <input id="username" v-model.trim="credentials.username" required maxlength="100" autocomplete="username">
                        <label for="email">Email</label>
                        <input id="email" v-model.trim="credentials.email" type="email" required maxlength="255" autocomplete="email">
                    </template>
                    <template v-else>
                        <label for="identity">Username or email</label>
                        <input id="identity" v-model.trim="credentials.identity" required autocomplete="username">
                    </template>
                    <label for="password">Password</label>
                    <input id="password" v-model="credentials.password" type="password" minlength="8" required autocomplete="current-password">
                    <button type="submit" :disabled="busy">{{ busy ? 'Please wait…' : (registering ? 'Create account' : 'Log in') }}</button>
                </form>
                <p class="auth-switch">
                    {{ registering ? 'Already registered?' : 'Need an account?' }}
                    <button @click="toggleRegistration">{{ registering ? 'Log in' : 'Register' }}</button>
                </p>
            </div>
        </section>

        <main v-else>
            <div class="intro">
                <div class="hero-eye" aria-hidden="true">
                    <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="eye-outline" d="M7 60C29 30 56 12 90 12s61 18 83 48c-22 30-49 48-83 48S29 90 7 60Z"/>
                        <circle class="iris-ring" cx="90" cy="60" r="34"/>
                        <circle class="iris" cx="90" cy="60" r="25"/>
                        <circle class="pupil" cx="90" cy="60" r="12"/>
                        <circle cx="96" cy="53" r="4" fill="#F4F7FF"/>
                    </svg>
                </div>
                <div>
                    <p class="eyebrow">Catalog</p>
                    <h1>Our products</h1>
                    <p class="subtitle">Manage product records through the authenticated LavaLust API.</p>
                </div>
                <div class="toolbar">
                    <div class="count">{{ products.length }} products</div>
                    <button @click="openCreate">Add product</button>
                </div>
            </div>

            <div v-if="error" class="notice">{{ error }}</div>
            <div v-if="notice" class="notice success">{{ notice }}</div>

            <form v-if="formOpen" class="form-card" @submit.prevent="saveProduct">
                <h2>{{ editingId ? 'Edit product' : 'Add product' }}</h2>
                <p>Save changes using the authenticated API.</p>
                <div class="form-grid">
                    <div>
                        <label for="product-name">Product name</label>
                        <input id="product-name" v-model.trim="draft.product_name" maxlength="100" required>
                    </div>
                    <div>
                        <label for="product-price">Price</label>
                        <input id="product-price" v-model="draft.price" type="number" min="0" max="99999999.99" step="0.01" required>
                    </div>
                    <div>
                        <label for="product-quantity">Quantity</label>
                        <input id="product-quantity" v-model="draft.quantity" type="number" min="0" step="1" required>
                    </div>
                    <div>
                        <label for="product-description">Description</label>
                        <textarea id="product-description" v-model.trim="draft.description" maxlength="10000"></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" :disabled="busy">{{ busy ? 'Saving…' : 'Save product' }}</button>
                    <button type="button" class="secondary" @click="closeForm">Cancel</button>
                </div>
            </form>

            <div v-if="loading" class="empty-state">Loading products from the API…</div>
            <div v-else-if="!products.length" class="empty-state">There are no products in the catalog yet.</div>
            <div v-else class="product-grid">
                <article v-for="product in products" :key="product.id" class="product-card">
                    <div class="product-visual" aria-hidden="true"><span>{{ (product.product_name || 'P').slice(0, 1).toUpperCase() }}</span></div>
                    <div class="product-body">
                        <h2>{{ product.product_name }}</h2>
                        <p class="description">{{ product.description || 'No description provided.' }}</p>
                        <div class="product-details">
                            <div>
                                <span class="detail-label">Price</span>
                                <span class="price">{{ Number(product.price).toFixed(2) }}</span>
                            </div>
                            <div class="quantity">
                                <span class="detail-label">In stock</span>
                                {{ product.quantity }}
                            </div>
                        </div>
                        <div class="actions" style="margin-top: 16px">
                            <button class="secondary" @click="openEdit(product)">Edit</button>
                            <button class="danger" :disabled="busy" @click="deleteProduct(product)">Delete</button>
                        </div>
                    </div>
                </article>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script>
        const { createApp } = Vue;
        const API_BASE = `${window.location.origin}/api`;

        createApp({
            data() {
                return {
                    accessToken: localStorage.getItem('access_token') || '',
                    refreshToken: localStorage.getItem('refresh_token') || '',
                    user: null,
                    products: [],
                    credentials: { username: '', email: '', identity: '', password: '' },
                    draft: { product_name: '', description: '', price: '', quantity: '' },
                    registering: false,
                    formOpen: false,
                    editingId: null,
                    loading: false,
                    busy: false,
                    error: '',
                    notice: ''
                };
            },
            async mounted() {
                if (this.accessToken) await this.loadUser();
            },
            methods: {
                async request(path, options = {}, retry = true) {
                    const headers = { 'Content-Type': 'application/json', ...(options.headers || {}) };
                    if (this.accessToken) headers.Authorization = `Bearer ${this.accessToken}`;
                    let response = await fetch(`${API_BASE}${path}`, { ...options, headers });
                    let result = await response.json().catch(() => ({}));

                    if (response.status === 401 && retry && this.refreshToken && path !== '/auth/refresh') {
                        const refreshed = await fetch(`${API_BASE}/auth/refresh`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ refresh_token: this.refreshToken })
                        });
                        const refreshResult = await refreshed.json().catch(() => ({}));
                        if (refreshed.ok && refreshResult.tokens) {
                            this.storeTokens(refreshResult.tokens);
                            return this.request(path, options, false);
                        }
                        this.clearSession();
                    }

                    if (!response.ok) throw new Error(result.error || 'The API request failed.');
                    return result;
                },
                storeTokens(tokens) {
                    this.accessToken = tokens.access_token;
                    this.refreshToken = tokens.refresh_token;
                    localStorage.setItem('access_token', this.accessToken);
                    localStorage.setItem('refresh_token', this.refreshToken);
                },
                clearSession() {
                    this.accessToken = '';
                    this.refreshToken = '';
                    this.user = null;
                    this.products = [];
                    localStorage.removeItem('access_token');
                    localStorage.removeItem('refresh_token');
                },
                async authenticate() {
                    this.error = '';
                    this.notice = '';
                    this.busy = true;
                    try {
                        let result;
                        if (this.registering) {
                            result = await this.request('/auth/register', {
                                method: 'POST',
                                body: JSON.stringify({
                                    username: this.credentials.username,
                                    email: this.credentials.email,
                                    password: this.credentials.password
                                })
                            }, false);
                        } else {
                            result = await this.request('/auth/login', {
                                method: 'POST',
                                body: JSON.stringify({
                                    identity: this.credentials.identity,
                                    password: this.credentials.password
                                })
                            }, false);
                        }
                        this.storeTokens(result.tokens);
                        this.user = result.user;
                        this.credentials.password = '';
                        await this.loadProducts();
                    } catch (error) {
                        this.error = error.message;
                    } finally {
                        this.busy = false;
                    }
                },
                toggleRegistration() {
                    this.registering = !this.registering;
                    this.error = '';
                    this.notice = '';
                },
                async loadUser() {
                    try {
                        const result = await this.request('/auth/me');
                        this.user = result.user;
                        await this.loadProducts();
                    } catch (error) {
                        this.error = error.message;
                        this.clearSession();
                    }
                },
                async loadProducts() {
                    this.loading = true;
                    this.error = '';
                    try {
                        const result = await this.request('/products');
                        this.products = result.data;
                    } catch (error) {
                        this.error = error.message;
                    } finally {
                        this.loading = false;
                    }
                },
                openCreate() {
                    this.editingId = null;
                    this.draft = { product_name: '', description: '', price: '', quantity: '' };
                    this.formOpen = true;
                    this.error = '';
                },
                openEdit(product) {
                    this.editingId = product.id;
                    this.draft = {
                        product_name: product.product_name,
                        description: product.description || '',
                        price: product.price,
                        quantity: product.quantity
                    };
                    this.formOpen = true;
                    this.error = '';
                },
                closeForm() {
                    this.formOpen = false;
                    this.editingId = null;
                },
                async saveProduct() {
                    this.busy = true;
                    this.error = '';
                    this.notice = '';
                    try {
                        const editing = this.editingId !== null;
                        await this.request(editing ? `/products/${this.editingId}` : '/products', {
                            method: editing ? 'PUT' : 'POST',
                            body: JSON.stringify({
                                ...this.draft,
                                price: Number(this.draft.price),
                                quantity: Number(this.draft.quantity)
                            })
                        });
                        this.closeForm();
                        this.notice = editing ? 'Product updated.' : 'Product added.';
                        await this.loadProducts();
                    } catch (error) {
                        this.error = error.message;
                    } finally {
                        this.busy = false;
                    }
                },
                async deleteProduct(product) {
                    if (!window.confirm(`Delete "${product.product_name}"? This cannot be undone.`)) return;
                    this.busy = true;
                    this.error = '';
                    this.notice = '';
                    try {
                        await this.request(`/products/${product.id}`, { method: 'DELETE' });
                        this.notice = 'Product deleted.';
                        await this.loadProducts();
                    } catch (error) {
                        this.error = error.message;
                    } finally {
                        this.busy = false;
                    }
                },
                async logout() {
                    try {
                        if (this.accessToken) {
                            await this.request('/auth/logout', {
                                method: 'POST',
                                body: JSON.stringify({ refresh_token: this.refreshToken })
                            }, false);
                        }
                    } catch (error) {
                        this.error = error.message;
                    } finally {
                        this.clearSession();
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
