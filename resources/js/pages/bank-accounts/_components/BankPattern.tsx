export type BankKey = 'BCA' | 'MANDIRI' | 'BNI' | 'BRI' | 'BSI' | 'DEFAULT';

export interface BankConfig {
    gradient: string;
    patternId: string;
    initials: string;
}

export const BANK_CONFIG: Record<BankKey, BankConfig> = {
    BCA: {
        gradient: 'from-blue-700 via-blue-600 to-cyan-600',
        patternId: 'bca-dots',
        initials: 'BCA',
    },
    MANDIRI: {
        gradient: 'from-blue-900 via-blue-800 to-yellow-600',
        patternId: 'mandiri-diag',
        initials: 'MDR',
    },
    BNI: {
        gradient: 'from-orange-600 via-red-600 to-orange-700',
        patternId: 'bni-hex',
        initials: 'BNI',
    },
    BRI: {
        gradient: 'from-blue-700 via-sky-600 to-cyan-500',
        patternId: 'bri-waves',
        initials: 'BRI',
    },
    BSI: {
        gradient: 'from-emerald-700 via-teal-600 to-emerald-700',
        patternId: 'bsi-mesh',
        initials: 'BSI',
    },
    DEFAULT: {
        gradient: 'from-zinc-800 via-zinc-700 to-zinc-800',
        patternId: 'default-grid',
        initials: '??',
    },
};

export function detectBankKey(bankName: string): BankKey {
    const upper = bankName.toUpperCase();
    if (upper.includes('BCA') || upper.includes('CENTRAL ASIA')) return 'BCA';
    if (upper.includes('MANDIRI')) return 'MANDIRI';
    if (upper.includes('BNI') || upper.includes('NEGARA INDONESIA')) return 'BNI';
    if (upper.includes('BRI') || upper.includes('RAKYAT INDONESIA')) return 'BRI';
    if (upper.includes('BSI') || upper.includes('SYARIAH INDONESIA')) return 'BSI';
    return 'DEFAULT';
}

export function getBankInitials(bankName: string): string {
    const key = detectBankKey(bankName);
    if (key !== 'DEFAULT') return BANK_CONFIG[key].initials;
    return bankName
        .split(/\s+/)
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('');
}

interface BankPatternProps {
    bankKey: BankKey;
    opacity?: number;
}

export default function BankPattern({ bankKey, opacity = 0.12 }: BankPatternProps) {
    const id = `pattern-${bankKey}-${Math.random().toString(36).slice(2, 7)}`;

    const patterns: Record<BankKey, React.ReactNode> = {
        BCA: (
            <pattern id={id} x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="2" cy="2" r="1.5" fill="white" />
                <circle cx="12" cy="12" r="1.5" fill="white" />
            </pattern>
        ),
        MANDIRI: (
            <pattern id={id} x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <line x1="0" y1="16" x2="16" y2="0" stroke="white" strokeWidth="1" />
            </pattern>
        ),
        BNI: (
            <pattern id={id} x="0" y="0" width="24" height="28" patternUnits="userSpaceOnUse">
                <polygon
                    points="12,2 22,7 22,21 12,26 2,21 2,7"
                    fill="none"
                    stroke="white"
                    strokeWidth="1"
                />
            </pattern>
        ),
        BRI: (
            <pattern id={id} x="0" y="0" width="40" height="20" patternUnits="userSpaceOnUse">
                <path d="M0,10 Q10,0 20,10 Q30,20 40,10" fill="none" stroke="white" strokeWidth="1.5" />
            </pattern>
        ),
        BSI: (
            <pattern id={id} x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <path d="M0,0 L20,0 M0,10 L20,10 M0,20 L20,20 M0,0 L0,20 M10,0 L10,20 M20,0 L20,20" stroke="white" strokeWidth="0.5" />
            </pattern>
        ),
        DEFAULT: (
            <pattern id={id} x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <path d="M0,0 L20,0 M0,20 L20,20 M0,0 L0,20 M20,0 L20,20" stroke="white" strokeWidth="0.5" />
            </pattern>
        ),
    };

    return (
        <svg className="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>{patterns[bankKey]}</defs>
            <rect width="100%" height="100%" fill={`url(#${id})`} opacity={opacity} />
        </svg>
    );
}
