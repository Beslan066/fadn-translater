import React, { useEffect, useState } from 'react';
import {  usePage } from "@inertiajs/react";

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import RegionStatistics from '@/Components/RegionStatistics';

export default function Dashboard({ auth }) {

    const {statistics} = usePage();



    return (
        <AuthenticatedLayout auth={auth}>
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h2 className="text-2xl font-bold mb-6">Статистика по регионам</h2>

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
